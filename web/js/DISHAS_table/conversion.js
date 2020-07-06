/**
 * @file Implements tools for manipulating historical values
 * @author Antonin Penon
 */

/* global nameToBase */
/* global expectedSignificants */

class Exception extends Error {
    constructor(type, message, parameter) {
        super(message);
        this.parameter = parameter;
        this.type = type;
    }
}

/**
 * Extract an integer from a String. Return NaN is the string does
 * not corresponds to an integer
 * @param  {String} val
 * @return {Number}
 */
function myParseInt(val) {
    if($.isNumeric(val)) {
        var res = Number(val);
        if(Number.isInteger(res)) {
            return res;
        }
        return NaN;
    }
    else
        return NaN;
}

/**
 * Function to pad a radix base to the left (i.e. integer part) if needed.
 * @param  {Array}  base    Radix base
 * @param  {Number} number  Minimum length desired
 * @return {undefined}
 */
function loopLeft(base, number) {
    while(base[0].length < number) {
        base[0].push(base[0][base[0].length-1]);
    }
}

/**
 * Function to pad a radix base to the right (i.e. fractionnal part) if needed.
 * @param  {Array}  base    Radix base
 * @param  {Number} number  Minimum length desired
 * @return {undefined}
 */
function loopRight(base, significant) {
    while(base[1].length < significant) {
        base[1].push(base[1][base[1].length-1]);
    }
}

/**
 * This function returns the coordinates of an index in the specified base.
 * The result is a couple of integers.
 * The first is an integer specifying whether the provided index corresponds
 * to the left part (integer part) of the number, or to the right part
 * (fractional part) of the number. The second number indicates the index in
 * the left or right base.
 * @param  {TypeOfNumber} base      
 * @param  {Number} index     position in the base, counting from left to right
 * @param  {Number} decindex  position of the separation fractionnal/integer (i.e. index of the last integer position)
 * @return {Array}
 */
function posInBase(base, index, decindex) {
    if(decindex === undefined)
        var decindex = base[0].length;
    if(index < decindex) {
        return [0,decindex-index-1];
    }
    return [1,index-decindex];
}

/* eslint-disable */
if(false) {
    /**
     * This module uses the concept of \ :js:attr:`TypeOfNumber`\ .
     * It represents a numeral system historically used to perform astronomical computations.
     * They consists of one list of radix for the integer part, one list of radix for the fractionnal part,
     * and a name. In this representation, the two radix lists are implicitly looped.
     * @example
     * // The sexagesimal system uses base 60 for both the integer and fractionnal part
     * {
     *     0: [60],             // integer part
     *     1: [60],             // fractionnal part
     *     name: "sexagesimal"
     * }
     * @example
     * // The historical system uses base 30 for signs, base 15 for revolutions and then a classic decimal system
     * // For the fractionnal part, the base 60 is used
     * {
     *     0: [30, 12, 10],                 // understand as [30, 12, 10, 10, 10, ...]
     *     1: [60],                         // understand as [60, 60, 60, ...]
     *     name: "historical",
     *     integerSeparators: ["s ", "r "]  // separators for string representations
     * }
     * @type {Object}
     */
    TypeOfNumber = undefined;
    /**
     * Alias of \ :js:attr:`TypeOfNumber`\ .
     * @type {Object}
     */
    RadixBase = undefined;
}
/* eslint-enable */

if(nameToBase === undefined)
    /**
     * In order to be used, this module needs a dictionary called nameToBase, containing the defintion of each
     * supported \ :js:attr:`TypeOfNumber`\ . In practice, this variable will be filled with a value passed
     * from the controller.
     * @example
     * nameToBase = {
     *     sexagesimal: {
     *         0: [60],
     *         1: [60],
     *         name: "sexagesimal"
     *     },
     *     integer_and_sexagesimal: {
     *         0: [10],
     *         1: [60],
     *         name: "integer_and_sexagesimal"
     *     },
     *     historical: {
     *         0: [30, 12, 10],
     *         1: [60],
     *         name: "historical",
     *         integerSeparators: ["s ", "r "]
     *     },
     *     ...
     * }
     * @type {Object}
     */
    nameToBase = {};

/*
 * All number representations are expressed as an Array [leftDigits, rightDigits, reminder],
 * Where : 
 *     leftDigits is the list of the number in the integer part
 *     rightDigits is the list of the number in the decimal part
 *     reminder is a 64bit float number allowing to reconstruct the exact decimal value
 */

class NView {
    /**
     * This class represent a quantity expressed in a specific \ :js:attr:`TypeOfNumber`\ .
     *
     * @example
     * // Rather than using the constructor of this class, it is more handy to call
     * // either NView.fromString or NView.float64ToBase
     * >> var sexa_number = NView.fromString('14, 23 ; 18, 29', nameToBase['sexagesimal']);
     * >> console.log(sexa_number.toString());
     * '14,23 ; 18,29'
     * >> var integer_and_sexa_number = NView.float64ToBase(1457.8542, nameToBase['integer_and_sexagesimal'], 5);
     * >> console.log(integer_and_sexa_number.toString());
     * "1457 ; 51,15,07,11,59"
     * 
     * @example
     * // We create with the constructor a Number View representing the sexagesimal number: 2, 45 ; 23, 57
     * >> var sexa_number = new NView([45, 2], [23, 57], 0, 1, nameToBase['sexagesimal']);
     * >> console.log(sexa_number.toString());
     * "02,45 ; 23,57"
     * >> console.log(sexa_number.toFloat64());
     * 165.39916666666667
     * 
     * @param  {Array}   leftList   List of values for the integer part
     * @param  {Array}   rightList  List of values for the fractionnal part
     * @param  {Number}  remainder  Remainder (in the case the number of fractionnal positions is not sufficient)
     * @param  {Number}  sign       Sign of the number (+/-1)
     * @param  {Array}   base       \ :js:attr:`TypeOfNumber`\  used to represent this quantity
     * @return {NView}
     */
    constructor(leftList, rightList, remainder, sign, base) {
        /**
         * List of values for the integer part. **They are indexed from right to left**.
         * @example
         * // We create a Number View representing the sexagesimal number: 2, 45 ; 23, 57
         * >> var sexa_number = new NView([45, 2], [23, 57], 0, 1, nameToBase['sexagesimal']);
         * >> console.log(sexa_number.toString());
         * "02,45 ; 23,57"
         * // its attribute leftList is:
         * >> console.log(sexa_number.leftList);
         * [45, 02]
         * @type {Array}
         */
        this.leftList = leftList;
        /**
         * List of values for the fractionnal part. They are indexed from left to right.
         * @example
         * // We create a Number View representing the sexagesimal number: 2, 45 ; 23, 57
         * >> var sexa_number = new NView([45, 2], [23, 57], 0, 1, nameToBase['sexagesimal']);
         * >> console.log(sexa_number.toString());
         * 02,45 ; 23,57
         * // its attribute rightList is:
         * >> console.log(sexa_number.rightList);
         * [23, 57]
         * @type {Array}
         */
        this.rightList = rightList;
        /**
         * When a computation is performed and the result must be placed in a \ :js:class:`NView`\  which does not have
         * enough significant places, the remainder is stored in this attribute.
         * @example
         * // We create a Number View representing the sexagesimal number: 2, 45 ; 23, 57, 01, 30
         * >> var sexa_number = new NView([45, 2], [23, 57, 01, 30], 0, 1, nameToBase['sexagesimal']);
         * >> console.log(sexa_number.toString());
         * 02,45 ; 23,57,01,30
         * // We resize it (no precision loss thanks to the remainder attribute, different from truncate)
         * >> sexa_number.resize(3);
         * >> console.log(sexa_number.toString());
         * 02,45 ; 23,57,01
         * // its remainder has been updated in order to keep the full precision of the quantity,
         * // despite the lack of significant places to represent it
         * >> console.log(sexa_number.remainder);
         * 0.5
         * // thus the number can be reconstructed if wished
         * >> sexa_number.resize(4);
         * >> console.log(sexa_number.toString());
         * 02,45 ; 23,57,01,30
         * >> console.log(sexa_number.remainder);
         * 0
         * @type {Number}
         */
        this.remainder = remainder;
        /**
         * Sign of the number: +/-1
         * @type {Number}
         */
        this.sign = sign;
        /**
         * \ :js:attr:`TypeOfNumber`\  used to represent this quantity.
         * @type {Object}
         */
        this.base = base;
        
        if(this.remainder === undefined)
            throw "Remainder undefined!";
        if(this.sign === undefined)
            throw "Sign undefined!";
        if(typeof(this.base) === "string")
            throw "Name of the base passed in the constructor instead of the base!";
        
        loopLeft(base, leftList.length);
        loopRight(base, rightList.length);
    }
    
    posToBase(pos) {
        if(pos <= 0) {
            return this.base[0][-pos];
        }
        return this.base[1][pos-1];
    }
    posToValue(pos) {
        if(pos <= 0) {
            return this.leftList[-pos];
        }
        return this.rightList[pos-1];
    }
    setValueAtPos(pos, value) {
        if(pos <= 0) {
            this.leftList[-pos] = value;
        }
        else {
            this.rightList[pos-1] = value;
        }
    }
    
    /**
     * Convert this quantity to a string representation.
     * This representation uses commas to separate the different positions, and a semi-column
     * to separate the integer part and the fractionnal part.
     * If the \ :js:attr:`TypeOfNumber`\  used has an attribute 'integerSeparators', this list will be used
     * insted of commas for the separation of integer places.
     * If the radix is 10, no separator is used.
     * If the base is named decimal, the standard representation with a dot is used.
     * @example
     * >> var sexa_number = new NView([45, 2], [23, 57, 01, 30], 0, 1, nameToBase['sexagesimal']);
     * >> console.log(sexa_number.toString());
     * "2,45 ; 23,57,1,30"
     * >> var historical_number = new NView([29, 10, 2, 3, 4], [13, 22], 0, 1, nameToBase['historical']);
     * >> console.log(historical_number.toString())
     * "432r 10s 29 ; 13,22"
     * >> var decimal_number = new NView([8, 1, 1], [2, 1, 8], 0, 1, nameToBase['decimal']);
     * >> console.log(decimal_number.toString())
     * "118 . 218"
     * @return {String}
     */
    toString() {
        var res = "";
        if(this.sign < 0)
            res += "-";
        if(this.leftList.length === 0)
            this.leftList.push(0);
        for(var i=this.leftList.length-1; i>=0; i--) {
            var num = String(this.leftList[i]);
            var digit = ndigitForRadix(this.base[0][i]);
            while(num.length < digit) {
                num = "0"+num;
            }
            res += num;
            if(i != 0) {
                if(this.base["integerSeparators"] !== undefined && i-1 < this.base["integerSeparators"].length && i-1 >= 0) {
                    res += this.base["integerSeparators"][i-1];
                }
                else {
                    if(this.base[0][i] !== 10)
                        res += ",";
                }
            }
        }
        if(this.base.name !== "decimal")
            res += " ; ";
        else
            res += ".";
        
        for(var i=0; i<this.rightList.length; i++) {
            var num = String(this.rightList[i]);
            digit = ndigitForRadix(this.base[1][i]);
            while(num.length < digit) {
                num = "0"+num;
            }
            res += num;
            if(i != this.rightList.length-1)
                if(this.base.name !== "decimal")
                    res += ",";
        }
        return res;
    }
    /**
     * Build a \ :js:class:`NView`\  object from a string representation
     * @example
     * >> var sexa_number = NView.fromString('02,45;23,57', nameToBase['sexagesimal']);
     * >> console.log(sexa_number.leftList)
     * [45, 2]
     * >> console.log(sexa_number.toFloat64())
     * 165.39916666666667
     * >> var historical_number = NView.fromString('123r 9s 29; 45,12,34', nameToBase['historical']);
     * >> var integer_sexagesimal_number = NView.fromString('1432; 27,37', nameToBase['integer_and_sexagesimal']);
     * 
     * @param  {String}  string  String from which the representation will be built
     * @param  {Array}   base    Type of number to use (i.e. radix base)
     * @return {NView}
     */
    static fromString(string, base) {
        string = string.trim().toLowerCase();
        if(string === "")
            throw new Exception("ParsingError", "String is empty", "Empty");
        if(string.charAt(0) === "-") {
            var sign = -1;
            string = string.substring(1);
        }
        else {
            var sign = 1;
        }
        if(base.name !== "decimal")
            var leftRight = string.split(";");
        else
            var leftRight = string.split(".");
        if(leftRight.length < 2) {
            var left = leftRight[0];
            var right = "";
        }
        else if(leftRight.length === 2) {
            var left = leftRight[0];
            var right = leftRight[1];
        }
        else {
            throw new Exception("ParsingError", "Too many semi-columns");
        }
        left = left.trim();
        right = right.trim();
        
        var leftNumbers = [];
        var rightNumbers = [];
        
        if(right != "") {
            if(base.name === "decimal")
                var rightNumberString = right.split("");
            else
                var rightNumberString = right.split(",");
            for(var i=0; i<rightNumberString.length; i++) {
                var num = myParseInt(rightNumberString[i].trim());
                if(isNaN(num)) {
                    throw new Exception("ParsingError", "Cannot convert " + rightNumberString[i].trim() + " to integer", rightNumberString[i].trim());
                }
                rightNumbers.push(num);
            }
        }
        
        if(base["integerSeparators"] != undefined && left != "") {
            for(var i=0; i<base["integerSeparators"].length; i++) {
                var leftNumberString = left.split(base["integerSeparators"][i].trim().toLowerCase());
                var num = myParseInt(leftNumberString[leftNumberString.length-1].trim());
                if(isNaN(num)) {
                    throw new Exception("ParsingError", "Cannot convert " + leftNumberString[leftNumberString.length-1].trim() + " to integer", leftNumberString[leftNumberString.length-1].trim());
                }
                leftNumbers.push(num);
                if(leftNumberString.length < 2) {
                    left = "";
                    break;
                }
                leftNumberString.splice(-1);
                left = leftNumberString.join(base["integerSeparators"][i].trim().toLowerCase());
            }
        }
        
        if(left != "") {
            if(base[0][base[0].length-1] !== 10)
                var leftNumberString = left.split(",");
            else
                var leftNumberString = left.split("");
            for(var i=0; i<leftNumberString.length; i++) {
                var num = myParseInt(leftNumberString[leftNumberString.length-1-i].trim());
                if(isNaN(num)) {
                    throw new Exception("ParsingError", "Cannot convert " + leftNumberString[leftNumberString.length-1-i].trim() + " to integer", leftNumberString[leftNumberString.length-1-i].trim());
                }
                leftNumbers.push(num);
            }
        }
        if(leftNumbers.length === 0)
            leftNumbers.push(0);
        var res = new NView(leftNumbers,rightNumbers,0.0,sign,base);
        return res.sanitize();
    }
    /**
     * Sanitize the representation if needed. More precisely, it will check whether all the values in this.leftList
     * and this.rightList are smaller than the radix of the base at this position.
     * This is a helper function.
     * @param  {Number} [pos=this.rightList.length]
     * @return {this}
     */
    sanitize(pos) {
        if(pos === undefined)
            var pos = this.rightList.length;
        if(pos === this.rightList.length) { // ensure the remainder is below 1
            this.setValueAtPos(pos, this.posToValue(pos) + Math.floor(this.remainder));
            this.remainder -= Math.floor(this.remainder);
        }
        
        this.naiveSanitize();
        if(this.leftList.length > 0 && this.leftList[this.leftList.length-1] < 0) {
            for(var i=0; i<this.leftList.length; i++) {
                this.leftList[i] *= -1;
            }
            for(var i=0; i<this.rightList.length; i++) {
                this.rightList[i] *= -1;
            }
            this.naiveSanitize();
            this.sign *= -1;
        }
        return this;
    }
    /**
     * Helper function for \ :js:func:`sanitize`\ 
     * @param  {Number} [pos=this.rightList.length]
     * @return {this}
     */
    naiveSanitize(pos) {
        if(pos === undefined)
            var pos = this.rightList.length;
        if(pos <= 0 && -pos >= this.leftList.length)
            return this;
        if(pos <= 0) {
            loopLeft(this.base, -pos + 1);
        }
        if(this.posToValue(pos) >= this.posToBase(pos)) {
            var factor = Math.floor(this.posToValue(pos)/this.posToBase(pos));
            this.setValueAtPos(pos, this.posToValue(pos) % this.posToBase(pos));
            if(pos-1 <= 0 && -(pos-1) >= this.leftList.length)
                this.leftList.push(0);
            this.setValueAtPos(pos-1, Math.floor(factor + this.posToValue(pos-1)));
        }
        if(this.posToValue(pos) < 0) {
            if(pos-1 <= 0 && -(pos-1) >= this.leftList.length)
                return this;
            var factor = -(1+Math.floor(-this.posToValue(pos)/this.posToBase(pos)));
            var modulo = (this.posToBase(pos) - ((-this.posToValue(pos)) % this.posToBase(pos)));
            if(modulo === this.posToBase(pos)) {
                modulo = 0;
                factor += 1;
            }
            this.setValueAtPos(pos, modulo);
            this.setValueAtPos(pos-1, Math.floor(factor + this.posToValue(pos-1)));
        }
        return this.naiveSanitize(pos-1);
    }
    /**
     * Creates and returns a deep copy of this Number View.
     * @return {NView}
     */
    copy() {
        var left = JSON.parse(JSON.stringify(this.leftList));
        var right = JSON.parse(JSON.stringify(this.rightList));
        return new NView(left, right, this.remainder, this.sign, this.base);
    }
    /**
     * Reset this \ :js:class:`NView`\ by copying the properties of an other one.
     * @param {NView}
     */
    set(other) {
        this.leftList = JSON.parse(JSON.stringify(other.leftList));
        this.rightList = JSON.parse(JSON.stringify(other.rightList));
        this.remainder = other.remainder;
        this.sign = other.sign;
        this.base = other.base;
        return this;
    }
    /**
     * Removes the trailing zero in this number
     * @return {undefined}
     */
    removeTrailingZeros() {
        var count = 0;
        for(var i=this.rightList.length-1; i>=0; i--) {
            if(this.rightList[i] !== 0)
                break;
            count++;
        }
        if(count === 0)
            return;
        this.rightList.splice(-count);
    }
    /**
     * Truncates or expand this \ :js:class:`NView`\ . If the number is truncated, its remainder will be adjusted
     * accordingly. If the number is expanded, it will be padded with zeros (to the right).
     * Thanks to the \ :js:attr:`remainder`\  attribute, the precision is not lost during this operation.
     * @example
     * >> var sexa_number = NView.fromString('02,45;23,57', nameToBase['sexagesimal']);
     * >> console.log(String(sexa_number));
     * 02,45 ; 23,57
     * >> sexa_number.resize(1);
     * >> console.log(String(sexa_number));
     * 02,45 ; 23
     * >> console.log(sexa_number.remainder);
     * 0.95
     * >> sexa_number.resize(3);
     * >> console.log(String(sexa_number));
     * 02,45 ; 23,57,00
     * 
     * @param  {Number}  significant  The number of significant places we wish for this quantity
     * @return {this}
     */
    resize(significant) {
        if(significant === this.rightList.length)
            return this;
        
        loopRight(this.base, significant);
        
        // convert the remainder into its flaoting value
        var factor = 1.0;
        for(var i=0; i<this.rightList.length; i++) {
            factor /= this.base[1][i];
        }
        var remainderValue = factor * this.remainder;
        
        if(significant > this.rightList.length) { // stretching the number
            this.remainder = 0.0;
            for(var i=0; i<significant - this.rightList.length; i++) {
                this.rightList.push(0);
            }
            this.add(NView.float64ToBase(this.sign * remainderValue, this.base, significant));
            return this;
        }
        else { // truncating the number and updating the remainder
            var factor = 1.0;
            for(var i=0; i<significant; i++) {
                factor /= this.base[1][i];
            }
            for(var i=significant; i<this.rightList.length; i++) {
                factor /= this.base[1][i];
                remainderValue += factor * this.rightList[i];
            }
            this.truncate(significant);
            this.add(NView.float64ToBase(this.sign * remainderValue, this.base, significant));
            return this;
        }
    }
    /**
     * Performs the addition between this number and another. The result will be stored in this object.
     * **This is the operator +=**
     * @example
     * >> var sexa_number = new NView([45, 2], [23, 57], 0, 1, nameToBase['sexagesimal']);
     * >> console.log(String(sexa_number));
     * 2,45 ; 23,57
     * >> var sexa_other = new NView([17], [41, 17, 29], 0, 1, nameToBase['sexagesimal']);
     * >> console.log(String(sexa_other));
     * 17 ; 41,17,29
     * >> sexa_number.add(sexa_other);
     * >> console.log(String(sexa_number));
     * 03,03 ; 05,14,29
     * 
     * @param {NView} other
     * @return {this}
     */
    add(o) { // += operator
        if(this.sign === 0)
            this.sign = 1;
        var other = o.copy();
        if(this.rightList.length < other.rightList.length) {
            this.resize(other.rightList.length);
        }
        if(this.rightList.length > other.rightList.length) {
            other.resize(this.rightList.length);
        }
        while(this.leftList.length < other.leftList.length) {
            this.leftList.push(0);
        }
        while(this.leftList.length > other.leftList.length) {
            other.leftList.push(0);
        }
        for(var pos=1-this.leftList.length; pos<this.rightList.length+1; pos++) {
            this.setValueAtPos(pos, this.posToValue(pos) + this.sign * other.sign * other.posToValue(pos));
        }
        this.remainder += this.sign * other.sign * other.remainder;
        this.sanitize();
        return this;
    }
    /**
     * Remove useless zeros in inetger part
     * @example
     * >> var sexa_number = new NView([17, 0], [41, 17, 29], 0, 1, nameToBase['sexagesimal']);
     * >> console.log(String(sexa_number));
     * 00,17 ; 41,17,29
     * >> sexa_number.simplifyIntegerPart();
     * >> console.log(String(sexa_number));
     * 17 ; 41,17,29
     * 
     * @return {this}
     */
    simplifyIntegerPart() { // remove useless zeros in integer part
        for(var i=this.leftList.length-1; i>=0; i--) {
            if(this.leftList[i] !== 0)
                break;
        }
        this.leftList.splice(i+1);
        return this;
    }
    /**
     * Truncate the number to the given fractionnal place.
     * 
     * @param  {Number} significant
     * @return {this}
     */
    truncate(significant) {
        if(significant > this.rightList.length) {
            return this;
        }
        this.remainder = 0.0;
        this.rightList.splice(significant);
        return this;
    }
    /**
     * Round the number to the given fractionnal place
     * 
     * @param  {Number} significant  
     * @return {this}
     */
    round(significant) {
        if(significant === undefined) {
            var significant = this.rightList.length;
        }
        this.resize(significant);
        if(this.remainder > 0.5) {
            this.addToPos(significant, this.sign * 1);
        }
        this.remainder = 0.0;
        return this;
    }
    
    addToPos(pos, nb) { // add a number to the nth position
        if(nb === 0)
            return this;
        // the user wants to add at a position outside the left list (to the "left" of the number)
        if(pos <= 0 && -pos >= this.leftList.length) {
            for(var i=0; i<this.leftList.length-pos+1; i++) {
                this.leftList.push(0);
            }
        }
        // The user wants to add at a position outside the right list (to the "right" of the number)
        // update the remainder accordingly
        if(pos-1 >= this.rightList.length) {
            // TODO
        }
        this.setValueAtPos(pos, this.posToValue(pos) + this.sign * nb);
        this.sanitize();
        return this;
    }
    /**
     * Test if this quantity is equal to another \ :js:class:`NView`\ .
     * If the number of significant places is different for the two numbers, the extra positions
     * on the most precise one must be 0, or this teset will return false.
     * The \ :js:attr:`remainder`\  attribute is NOT tested.
     * 
     * @param  {NView} object  Number to compare to
     * @return {Boolean}
     */
    equals(object) {
        if(!checkListSimilarity(this.leftList, object.leftList))
            return false;
        if(!checkListSimilarity(this.rightList, object.rightList))
            return false;
        if(!checkListQualifyingNumber(this.leftList, object.leftList))
            return false;
        if(!checkListQualifyingNumber(this.rightList, object.rightList))
            return false;
        return true;
    }
    /**
     * Builds a \ :js:class:`NView`\  object from a floating decimal.
     * If the number of significant places is too small for the conversion, the \ :js:attr:`remainder`\  attribute
     * of the created object will be updated.
     * 
     * @param  {Number}  float        float representation of the quantity
     * @param  {Array}   base         the \ :js:attr:`TypeOfNumber`\  of the new \ :js:class:`NView`\  object
     * @param  {Number}  significant  the number of significant places
     * @return {NView}
     */
    static float64ToBase(float, base, significant) {
        var sign = Math.sign(float);
        
        //turn float into a positive number
        float *= sign;
        
        // compute the number of necessary places for the integer part
        var pos = 0;
        var maxInteger = 1;
        
        while(float >= maxInteger) {
            loopLeft(base, pos+1);
            maxInteger *= base[0][pos];
            pos++;
        }
        
        var left = [];
        var right = [];
        for(var i=0; i<pos; i++)
            left.push(0);
        for(var i=0; i<significant; i++)
            right.push(0);
        
        loopRight(base, significant);
        
        if(!isFinite(maxInteger)) {
            //console.log("Max precision reached!");
            return new NView(left, right, 0.0, sign, base);
        }
        
        var factor = maxInteger;
        //compute the integer part
        for(var i=pos-1; i>=0; i--) {
            factor /= base[0][i];
            var positionValue = Math.floor(float/factor);
            float -= positionValue * factor;
            left[i] = positionValue;
        }

        if(left.length === 0)
            left.push(0);
        
        //compute the fractionnal part
        var factor = 1.0;
        for(var i=0; i<significant; i++) {
            factor /= base[1][i];
            var positionValue = Math.floor(float/factor);
            float -= positionValue * factor;
            if(!isFinite(positionValue)) {
                //console.log("Max precision reached!");
                return new NView(left, right, 0.0, sign, base);
            }
            right[i] = positionValue;
        }
        
        var remainder = float/factor;
        if(!isFinite(remainder)) {
            //console.log("Max precision reached!");
            return new NView(left, right, 0.0, sign, base);
        }
        return new NView(left, right, remainder, sign, base);
    }
    /**
     * Convert this \ :js:class:`NView`\  to its floating-point value.
     * 
     * @return {Number}
     */
    toFloat64() {
        var dec = 0.0;
        var factor = 1.0;
        for(var i=0; i<this.leftList.length; i++) {
            dec += factor * this.leftList[i];
            factor *= this.base[0][i];
        }
        factor = 1.0;
        for(var i=0; i<this.rightList.length; i++) {
            factor /= this.base[1][i];
            dec += factor * this.rightList[i];
        }
        
        // ajout du remainder
        dec += factor * this.remainder;
        return dec * this.sign;
    }
    /**
     * Convert this \ :js:class:`NView`\  to an integer (its fractionnal part is discarded)
     * 
     * @return {Number}
     */
    toInt() {
        var dec = 0.0;
        var factor = 1.0;
        for(var i=0; i<this.leftList.length; i++) {
            dec += factor * this.leftList[i];
            factor *= this.base[0][i];
        }
        return dec * this.sign;
    }
    /**
     * Convert this \ :js:class:`NView`\  to another \ :js:attr:`TypeOfNumber`\ .
     * 
     * @param  {Array}  base         the target \ :js:attr:`TypeOfNumber`\ 
     * @param  {Number} significant  the desired number of significant places
     * @return {NView}               the newly created \ :js:class:`NView`\  object
     */
    toBase(base, significant) {
        loopRight(base, significant);
        // integer part
        var result = NView.intToBase(this.sign * this.toInt(), base, significant);
        // fractionnal part
        for(var i=0; i<this.rightList.length; i++) {
            var inter = NView.fractionnalPositionBaseToBase(this.rightList[i], i, this.base, base, significant);
            result.add(inter);
        }
                
        // covnert the remainder into its floating value
        var factor = 1.0;
        for(var i=0; i<this.rightList.length; i++) {
            factor /= this.base[1][i];
        }
        var remainderValue = factor * this.remainder;
        result.add(NView.float64ToBase(remainderValue, base, significant));
        
        result.sign = this.sign;
        return result;
    }
    /**
     * Helper function to convert one fractionnal place in one radix base to another radix base.
     * @param  {Number} value       Value at the given position
     * @param  {Number} pos         Position in the original radix base
     * @param  {Array}  base1       Original radix base
     * @param  {Array}  base2       Target radix base
     * @param  {Number} significant Precision (number of significant places) in the target radix base
     * @return {NView}
     */
    static fractionnalPositionBaseToBase(value, pos, base1, base2, significant) {
        var left = [0];
        var right = [];
        for(var i=0; i<significant; i++)
            right.push(0);
        
        var denom = 1;
        for(var i=0; i<=pos; i++) {
            denom *= base1[1][i];
        }
        if(!isFinite(denom)) {
            //console.log("Max precision reached!");
            return new NView(left, right, 0.0, 1, base2);
        }
        var num = value;
        for(var i=0; i<significant; i++) {
            num *= base2[1][i];
            var quo = Math.floor(num/denom);
            var rem = (num % denom);
            if(!isFinite(num) || !isFinite(quo) || !isFinite(rem)) {
                //console.log("Max precision reached!");
                return new NView(left, right, 0.0, 1, base2);
            }
            right[i] = quo;
            num = rem;
        }
        
        var remainder = rem/denom;
        if(!isFinite(remainder)) {
            //console.log("Max precision reached!");
            return new NView(left, right, 0.0, 1, base2);
        }
        
        return new NView(left, right, remainder, 1, base2);
    }
    /**
     * Builds a \ :js:class:`NView`\  object from an integer.
     * 
     * @param  {Number}  int          integer representation of the quantity
     * @param  {Array}   base         \ :js:attr:`TypeOfNumber`\  of the new \ :js:class:`NView`\  object
     * @param  {Number}  significant  the number of significant places
     * @return {NView}
     */
    static intToBase(int, base, significant) {
        var sign = Math.sign(int);
        int *= sign;
        var pos = 0;
        var maxInteger = 1;
        while(int >= maxInteger) {
            loopLeft(base, pos+1);
            maxInteger *= base[0][pos];
            pos++;
        }
        
        var left = [];
        for(var i=0; i<pos; i++)
            left.push(0);
        var right = [];
        for(var i=0; i<significant; i++)
            right.push(0);
        
        var factor = maxInteger;
        for(var i=pos-1; i>=0; i--) {
            factor /= base[0][i];
            var positionValue = Math.floor(int/factor);
            int -= positionValue * factor;
            left[i] = positionValue;
        }
        
        var remainder = 0.0;
        return new NView(left, right, remainder, sign, base);
    }
}

/**
 * Checks if 2 lists are equals up to min(arr1.length, arr2.length)
 * @param  {Array} arr1  First list
 * @param  {Array} arr2  Second list
 * @return {Boolean}
 */
function checkListSimilarity(arr1, arr2) {
    for(var i=0; i<Math.min(arr1.length, arr2.length); i++) {
        if(arr1[i] !== arr2[i])
            return false;
    }
    return true;
}

/**
 * Checks if 2 lists are equals. If one list is longer than the other, returns false
 * except if there are only trailing zeros in the remaining of the bigger list
 * @param  {Array} arr1  First list
 * @param  {Array} arr2  Second list
 * @return {Boolean}
 */
function checkListQualifyingNumber(arr1, arr2) {
    var a = arr1.length;
    var b = arr2.length;
    if(a < b) {
        var maxLeftObject = arr2;
        var numberOfExpectedZeros = a - b;
    }
    else {
        var maxLeftObject = arr1;
        var numberOfExpectedZeros = b - a;
    }
    if(!isFollowedByZeros(maxLeftObject, maxLeftObject.length - numberOfExpectedZeros))
        return false;
    return true;
}

/**
 * Helper function to check if an array is filled with zeros starting from a given index
 * @param  {Array}   arr1   First list
 * @param  {Number}  index
 * @return {Boolean}
 */
function isFollowedByZeros(arr, index) {
    for(var i=index; i<arr.length; i++) {
        if(arr[i] !== 0)
            return false;
    }
    return true;
}

expectedSignificants = {};

/**
 * Function returning the number of digits needed to represent one position for a given radix
 * @param  {Number} radix
 * @return {Number}
 */
function ndigitForRadix(radix) {
    return Math.ceil(Math.log10(radix));
}

/**
 * expectedSignificant
 * 
 * Returns the number of usefull positions in the fractionnal part.
 * Supplementary decimal position would be discarded during a conversion to 64bit floating number,
 * as they represent to much significant numbers.
 * 
 * @param {Array}   base
 * @param {Number}  nbleft
 * @return {Number}
 */
function expectedSignificant(basename,nbleft) {
    var base = nameToBase[basename];
    if(nbleft === undefined)
        var nbleft = 0;
    var number = base[0].length + base[1].length + nbleft;
    var prev = 1.0*Math.pow(base[0][0],nbleft);
    for(var i=0;i<base[0].length;i++)
        prev *= base[0][i];
    var factor = 1.0;
    for(var i=0;i<base[1].length;i++)
        factor /= base[1][i];
    for(var k=0; k<1000; k++) {
        factor /= base[1][base[1].length-1];
        var current = prev + factor;
        if(current === prev)
            break;
        number += 1;
    }
    return number-1;
}

for(var type in nameToBase)
    expectedSignificants[type] = expectedSignificant(type);

/**
 * Function to build a \ :js:class:`SmartNumber`\  object from a string representation
 * @param {string} string   string representation of the number
 * @param {Array}  basename radix base (i.e. type of number) to be sued
 * @return {SmartNumber}
 */
function SNFromStringBase(string, basename) {
    var nv = NView.fromString(string, nameToBase[basename]);
    return new SmartNumber(nv);
}


class SmartNumber {
    /**
     * Class allowing to store and convert numbers in different formats.
     * A \ :js:class:`SmartNumber`\  is created from an initial representation in a given \ :js:attr:`TypeOfNumber`\ s. This
     * number can then be converted in several \ :js:attr:`TypeOfNumber`\ , each time storing the new computed representation
     * for efficiency.
     * A \ :js:class:`SmartNumber`\  object can be created from a a float number or from a \ :js:class:`NView`\  object.
     * Alternately, one can create it by specfying its value list and radix base (similar to when creating a \ :js:class:`NView`\  object)
     * @example
     * // The 2 standard ways to create a SmartNumber:
     * >> var sn1 = new SmartNumber(72.24593518518519);
     * >> var sn3 = new SmartNumber('1, 12; 14, 45, 22', 'sexagesimal');
     * // 2 other ways to do it:
     * >> var sn2 = new SmartNumber(NView.fromString('1, 12; 14, 45, 22', nameToBase['sexagesimal']));
     * >> var sn4 = new SmartNumber([12, 1], [14, 45, 22], 0.0, 1, 'sexagesimal');
     * 
     * // The main usage is conversion:
     * >> String(sn3);
     * "01,12 ; 14,45,22"
     * >> sn3.computeDecimal();
     * 72.24593518518519
     * >> String(sn3.computeBase('historical', 4));
     * "02s 12 ; 14,45,22,00"
     * 
     * @param  {Object} arg1      |multi| Float value or \ :js:class:`NView`\  |or| the list of integer values in the wished radix base |_multi| 
     * @param  {Array}  rightList |multi| undefined                                       |or| the list of fractionnal values in the wished radix base |_multi| 
     * @param  {Number} remainder |multi| undefined                                       |or| the remainder of the new \ :js:class:`NView`\  object |_multi| 
     * @param  {Number} sign      |multi| undefined                                       |or| the sign +/-1 of the number |_multi| 
     * @param  {string} base      |multi| undefined                                       |or| the name of the radix base to be used |_multi| 
     * @return {SmartNumber}
     */
    constructor(arg1, rightList, remainder, sign, base) {
        /**
         * This dictionary holds the representation of the quantity (i.e. the \ :js:class:`NView`\ object)
         * in the different bases in which it has been converted.
         * @type {Object}
         */
        this.tobases = {};
        /**
         * Contains the float representation of the quantity
         * @type {Number}
         */
        this.decimal = undefined;
        /**
         * Contains the name of the original representation of the quantity.
         * This allow to prevent loss of precision during conversions.
         * @type {String}
         */
        this.initialbase = "none";

        if(rightList === undefined && remainder === undefined && sign === undefined && base === undefined) {
            if(typeof arg1 !== "number") {
                this.tobases[arg1.base.name] = new NView(JSON.parse(JSON.stringify(arg1.leftList)), JSON.parse(JSON.stringify(arg1.rightList)), arg1.remainder, arg1.sign, arg1.base);
                this.initialbase = arg1.base.name;
            }
            else {
                this.decimal = arg1;
                this.initialbase = "none";
            }
        }
        else if(rightList !== undefined && remainder === undefined && sign === undefined && base === undefined) {
            this.initialbase = rightList;
            this.tobases[this.initialbase] = NView.fromString(arg1, nameToBase[this.initialbase]);
            
        }
        else {
            this.tobases[base] = new NView(arg1, rightList, remainder, sign, nameToBase[base]);
            this.initialbase = base;
        }
    }
    /**
     * Destroy all computed representations of the number, except for the initial one.
     * @return {SmartNumber}
     */
    purge() {
        if(this.initialbase === "none")
            this.tobases = {};
        else {
            this.decimal = undefined;
            for(var type in this.tobases)
                if(type !== this.initialbase) {
                    delete this.tobases[type];
                }
        }
        return this;
    }
    /**
     * Returns the original representation of the quantity (a \ :js:class:`NView`\  object **or** a float number)
     * @return {} the original representation of the number
     */
    value() {
        if(this.initialbase === "none")
            return this.decimal;
        else
            return this.tobases[this.initialbase];
    }
    /**
     * Returns the number of significant fractional places of the original representation of the quantity
     * @return {Number}
     */
    precision() {
        if(this.initialbase === "none")
            return 8;
        else
            return this.tobases[this.initialbase].rightList.length;
    }
    /**
     * Returns true if this \ :js:class:`SmartNumber`\  is zero
     * @return {Boolean}
     */
    isZero() {
        if(this.initialbase === "none")
            return this.decimal === 0.0;
        else
            return this.tobases[this.initialbase].toFloat64() === 0.0;
    }
    /**
     * If not already done, compute the decimal value (as a 64bit float) of the number, returns it and stores it for later use
     * @return {Number}
     */
    computeDecimal() {
        if(this.decimal !== undefined)
            return this.decimal;
        this.decimal = this.tobases[this.initialbase].toFloat64();
        return this.decimal;
    }
    /**
     * If not already done, compute, returns and stores for a later use the representation of the number in a given base.
     * @param {string}  name
     * @param {Number}  significant
     * @return {NView}
     */
    computeBase(name,significant) {
        var toRound = false;
        if(significant === undefined) {
            if(this.tobases.hasOwnProperty(name))
                return this.tobases[name];
            significant = expectedSignificants[name] - 1; //on discard la decimale erronee
        }
        if((this.tobases.hasOwnProperty(name) && this.tobases[name].rightList.length >= significant))
            return this.tobases[name];
        
        if(this.initialbase === 'none') {
            if(this.decimal === undefined)
                this.computeDecimal();
            this.tobases[name] = NView.float64ToBase(this.decimal,nameToBase[name],significant);
        }
        else {
            this.tobases[name] = this.tobases[this.initialbase].toBase(nameToBase[name],significant);
        }
        if(toRound)
            this.tobases[name] = this.tobases[name].round(significant);
        return this.tobases[name];
    }
    /**
     * Compute the decimal value of the number, and convert it (as if it had been entered as a decimal value).
     * @example
     * >> var sn3 = new SmartNumber('1, 12; 14, 45, 22', 'sexagesimal');
     * >> String(sn3);
     * "01,12 ; 14,45,22"
     * >> sn3.asDecimal();
     * >> String(sn3);
     * "72.24593518518519"
     * 
     * @return {this}
     */
    asDecimal() {
        this.computeDecimal();
        this.initialbase = "none";
        this.purge();
        return this;
    }
    /**
     * Compute the representation of the number in a given base, and convert it (as if it had been entered as representation of this base).
     * @example
     * >> var sn3 = new SmartNumber('1, 12; 14, 45, 22', 'sexagesimal');
     * >> String(sn3);
     * "01,12 ; 14,45,22"
     * >> sn3.asBase('historical', 4);
     * >> String(sn3);
     * "02s 12 ; 14,45,22,00"
     * 
     * @param {string}  name  name of the base
     * @param {Number}  significant
     * @return {this}
     */
    asBase(name, significant) {
        this.computeBase(name, significant);
        this.initialbase = name;
        this.purge();
        return this;
    }
    /**
     * Truncate the number representation up to the nth decimal.
     * @param  {Number} ndec
     * @return {SmartNumber}
     */
    truncate(ndec) {
        if(this.initialbase === "none") {
            this.decimal = Math.floor(this.decimal * Math.pow(10.0,ndec))/Math.pow(10.0,ndec);
        }
        else {
            this.tobases[this.initialbase] = this.tobases[this.initialbase].truncate(ndec);
        }
        this.purge();
        return this;
    }
    /**
     * Round the number representation up to the nth decimal.
     * @param  {Number} ndec
     * @return {SmartNumber}
     */
    round(ndec) {
        if(this.initialbase === "none") {
            this.decimal = Math.round(this.decimal * Math.pow(10.0,ndec))/Math.pow(10.0,ndec);
        }
        else {
            this.tobases[this.initialbase] = this.tobases[this.initialbase].round(ndec);
        }
        this.purge();
        return this;
    }
    /**
     * Discard useless decimals (right-most decimals which don't count in 64bit representation)
     * @param {Number}  offset
     * @return {SmartNumber}
     */
    roundToFloat64Precision(offset) {
        if(offset === undefined)
            var offset = 1;
        this.round(expectedSignificants[this.initialbase]-offset);
        return this;
    }
    /**
     * Return a copy of this number
     * @return {SmartNumber}
     */
    copy() {
        if(this.initialbase === "none") {
            var res = new SmartNumber(this.decimal);
            return res;
        }
        var res = new SmartNumber(this.tobases[this.initialbase]);
        return res;
    }
    /**
     * Return a copy of this number, converted as a decimal one
     * @return {SmartNumber}
     */
    toDecimal() {
        var res = this.copy();
        return res.asDecimal();
    }
    /**
     * Resturn a copy of this number, converted in the given base
     * @param {string}  name  name of the base
     * @param {Number}  significant
     * @return {SmartNumber}
     */
    toBase(name, significant) {
        var res = this.copy();
        return res.asBase(name, significant);
    }
    /**
     * return a string representation of the number.
     * @param {string}  name  name of the base
     * @return {string}
     */
    toStringBase(name, significant) {
        if(name === undefined)
            var name = "none";
        if(name === "none") {
            this.computeDecimal();
            return this.decimal.toString();
        }
        if(!this.tobases.hasOwnProperty(name)) {
            this.computeBase(name, 10);
        }
        if(significant !== undefined)
            return this.tobases[name].resize(significant).toString();
        return this.tobases[name].toString();
    }
    /**
     * Override of the toString method.
     * Returns a string representation of this quantity.
     * @param {Number}  radix
     * @returns {string}
     */
    toString(radix) {
        return this.toStringBase(this.initialbase);
    }
    /**
     * Add the given value to a chosen position in the \ :js:class:`SmartNumber`\ 
     * @example
     * >> var sn3 = new SmartNumber('1, 12; 14, 45, 22', 'sexagesimal');
     * >> String(sn3);
     * "01,12 ; 14,45,22"
     * >> sn3.addOne(3, 20);
     * >> String(sn3);
     * "01,12 ; 15,05,22"
     * 
     * @param {Number} pos  Position we wish to increment (e.g. in the number 1, 12; 45, 59  the value 12 is in position 1)
     * @param {Number} nb   The increment
     */
    addOne(pos, nb) {
        if(nb === undefined)
            var nb = 1;
        var pos = pos - this.tobases[this.initialbase].leftList.length + 1;
        this.purge();
        this.tobases[this.initialbase].addToPos(pos,nb);
    }
}

/**
 * Example code for storing and converting numbers.
 * 
 * test1 = new SmartNumber(53.14);
 * console.log(''+test1);
 * console.log(''+test1.toBase('historical',3));
 * console.log(''+test1.toBase('historical').roundToFloat64Precision(0));
 * console.log(''+test1.toBase('historical').toDecimal());
 * console.log(''+test1.toBase('historical').roundToFloat64Precision(0).toDecimal());
 * test1.asBase('historical');
 * console.log(''+test1);
 */


/*test1 = new SmartNumber(55.1445675);
console.log(''+test1);
console.log(''+test1.toBase('historical',3));*/

/*test2 = new SmartNumber([2,1,7],[4,5,3,0,7,9,2,3,5,1,4,7,8,5,2,1,0,9,8,5,3,2,4,2,7,7,2,0,0,0,0,0,0,0,7,0,0],0.0,1,'decimal');
console.log(''+test2);
//console.log(''+test2.toBase('sexagesimal',300));
console.log("ok this far");
console.log(''+test2.asBase('sexagesimal',300).toBase('decimal',37).round());

console.log("suite");
test3 = new SmartNumber([0],[27,11,7,15,12,8,20,39,6,3,27,24,29,17,49,16,58,54,10,44,33,29],0.0,1,'sexagesimal');
console.log(''+test3);
console.log(''+test3.asBase('decimal',20));
console.log("finished!");*/

/*
test2 = new SmartNumber([4,2,1,7],[1,2,3,4,5,6,7,8,9,1,4,5,3,0,8,9,1,3,5,9,8,1,3,1,4,1,8,7,1,4,6,8,5,7,8,9,5,2,3,1,0,1,4,7,8,8,8],'decimal');
console.log(''+test2);
console.log(''+test2.toBase('sexagesimal',30));
console.log(''+test2.toBase('decimal',20).round(47));
*/
