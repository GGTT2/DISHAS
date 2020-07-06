/* jshint esversion: 6 */

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * **
 *                            BOOTSTRAP ANIMATION                            *
 ** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * **/
$(function () { // enable bootstrap/jquery popover
    $('[data-toggle="popover"]').popover();
});
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
$(".noClick").click(function (e) {
    e.preventDefault();
});

/**
 * Trigger (Work in progress)
 */
$(".wip").click(function (e) {
    e.preventDefault();
    let dialogWip = $("#dialog-wip");
    dialogWip.removeClass('hidden');
    dialogWip.dialog({
        modal: true
    });
});

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * **
 *                            STRING FORMATTING                            *
 ** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * **/

String.prototype.truncate = function (length) {
    return this.length > length ? this.substring(0, length - 1) + '…' : this;
};

/**
 * Turns to uppercase the first letter of a string
 * @return {string}
 */
String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

/**
 * Convert a string to Pascal Case (removing non alphabetic characters).
 *
 * @example
 * 'hello_world'.toPascalCase() // Will return `HelloWorld`.
 * 'fOO BAR'.toPascalCase()     // Will return `FooBar`.
 *
 * @returns {string}
 *   The Pascal Cased string.
 */
String.prototype.toPascalCase = function () {
    return this.match(/[a-z]+/gi)
        .map(function (word) {
            return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
        })
        .join('');
};

String.prototype.toCamelCase = function () {
    return this.replace(/(?:^\w|[A-Z]|\b\w|\s+)/g, function(match, index) {
        if (+match === 0) return ""; // or if (/\s+/.test(match)) for white spaces
        return index === 0 ? match.toLowerCase() : match.toUpperCase();
    });
    /*let pascalCase = this.match(/[a-z]+/gi)
        .map(function (word) {
            return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
        })
        .join('');
    return pascalCase.charAt(0).toLowerCase() + pascalCase.substr(1);*/
};

String.prototype.trimHTML = function () {
    if ((this === null) || (this === '') || (typeof this === "undefined"))
        return false;
    else
        return this.toString().replace(/<[^>]*>/g, '');
};
const plural = {
    '(quiz)$'               : "$1zes",
    '^(ox)$'                : "$1en",
    '([m|l])ouse$'          : "$1ice",
    '(matr|vert|ind)ix|ex$' : "$1ices",
    '(x|ch|ss|sh)$'         : "$1es",
    '([^aeiouy]|qu)y$'      : "$1ies",
    '(hive)$'               : "$1s",
    '(?:([^f])fe|([lr])f)$' : "$1$2ves",
    '(shea|lea|loa|thie)f$' : "$1ves",
    'sis$'                  : "ses",
    '([ti])um$'             : "$1a",
    '(tomat|potat|ech|her|vet)o$': "$1oes",
    '(bu)s$'                : "$1ses",
    '(alias)$'              : "$1es",
    '(octop)us$'            : "$1i",
    '(ax|test)is$'          : "$1es",
    '(us)$'                 : "$1es",
    '([^s]+)$'              : "$1s"
};

const singular = {
    '(quiz)zes$'             : "$1",
    '(matr)ices$'            : "$1ix",
    '(vert|ind)ices$'        : "$1ex",
    '^(ox)en$'               : "$1",
    '(alias)es$'             : "$1",
    '(octop|vir)i$'          : "$1us",
    '(cris|ax|test)es$'      : "$1is",
    '(shoe)s$'               : "$1",
    '(o)es$'                 : "$1",
    '(bus)es$'               : "$1",
    '([m|l])ice$'            : "$1ouse",
    '(x|ch|ss|sh)es$'        : "$1",
    '(m)ovies$'              : "$1ovie",
    '(s)eries$'              : "$1eries",
    '([^aeiouy]|qu)ies$'     : "$1y",
    '([lr])ves$'             : "$1f",
    '(tive)s$'               : "$1",
    '(hive)s$'               : "$1",
    '(li|wi|kni)ves$'        : "$1fe",
    '(shea|loa|lea|thie)ves$': "$1f",
    '(^analy)ses$'           : "$1sis",
    '((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$': "$1$2sis",
    '([ti])a$'               : "$1um",
    '(n)ews$'                : "$1ews",
    '(h|bl)ouses$'           : "$1ouse",
    '(corpse)s$'             : "$1",
    '(us)es$'                : "$1",
    's$'                     : ""
};

const irregular = {
    'move'   : 'moves',
    'foot'   : 'feet',
    'goose'  : 'geese',
    'sex'    : 'sexes',
    'child'  : 'children',
    'man'    : 'men',
    'tooth'  : 'teeth',
    'person' : 'people'
};

const uncountable = [
    'sheep',
    'fish',
    'deer',
    'moose',
    'series',
    'species',
    'money',
    'rice',
    'information',
    'equipment'
];

String.prototype.pluralize = function (){
    // save some time in the case that singular and plural are the same
    if (uncountable.indexOf(this.toLowerCase()) >= 0)
        return this;

    // check for irregular forms
    for (let i = Object.keys(irregular).length - 1; i >= 0; i--) {
        const word = Object.keys(irregular)[i];
        const pattern = new RegExp(word+'$', 'i');

        if (pattern.test(this))
            return this.replace(pattern, irregular[word]);
    }

    // check for matches using regular expressions
    for (let i = Object.keys(plural).length - 1; i >= 0; i--) {
        const reg = Object.keys(plural)[i];
        const pattern = new RegExp(reg, 'i');

        if (pattern.test(this))
            return this.replace(pattern, plural[reg]);
    }
    return this;
};

String.prototype.singularize = function (){
    // save some time in the case that singular and plural are the same
    if (uncountable.indexOf(this.toLowerCase()) >= 0)
        return this;

    // check for irregular forms
    for (let i = Object.keys(irregular).length - 1; i >= 0; i--) {
        const word = Object.keys(irregular)[i];
        const pattern = new RegExp(irregular[word]+'$', 'i');

        if (pattern.test(this))
            return this.replace(pattern, word);
    }

    // check for matches using regular expressions
    for (let i = Object.keys(plural).length - 1; i >= 0; i--) {
        const reg = Object.keys(plural)[i];
        const pattern = new RegExp(reg, 'i');

        if (pattern.test(this))
            return this.replace(pattern, singular[reg]);
    }
    return this;
};

/**
 * @param string string
 * @return {string}
 */
const pluralize = (string) => {
    if (string.endsWith("s")){
        return string + "es";
    } else if (string.endsWith("y")){
        return string.substring(0, string.length-1) + "ies";
    }
    return string + "s";
};

function truncateString(str, length) {
    return str.length > length ? str.substring(0, length - 3) + '...' : str;
}

/**
 * turns to uppercase the first letter of a string
 * @param s
 * @return {string}
 */
const capitalize = (s) => {
    if (typeof s !== 'string') return '';
    return s.charAt(0).toUpperCase() + s.slice(1);
};

/**
 * takes a string in snake_case or kebab-case and transform it to camelCase
 * @param s
 * @return {*}
 */
const toCamel = (s) => {
    return s.replace(/([-_][a-z])/ig, ($1) => {
        return $1.toUpperCase()
            .replace('-', '')
            .replace('_', '');
    });
};

/**
 * takes a string in normal case ("Original text") or kebab-case ("Original-text") and transform it to snake_case ("original_text")
 * @param s
 * @return {*}
 */
const toSnake = (s) => {
    return s.toLowerCase().replace(/[- ]([a-z])/ig, ($1) => {
        return $1.toLowerCase()
            .replace('-', '')
            .replace(' ', '_');
    });
};

/**
 * Takes a string in camelCase and transform it to snake_case
 * @param s
 * @return {string}
 */
const camelToSnake = (s) => {
    return s.replace(/[\w]([A-Z])/g, function(m) {
        return m[0] + "_" + m[1];
    }).toLowerCase();
};

/**
 * This function trim all html tag from a string
 * @param str
 * @return {string|boolean}
 */
function trimHtmlTags(str)
{
    if ((str === null) || (str === '') || (typeof str === "undefined"))
        return false;
    else
        return str.toString().replace(/<[^>]*>/g, '');
}

/**
 * Truncate string that might contain HTML tag (remove HTML and put it back)
 *
 * @param string {string}
 * @param max {int}
 * @param extra {string}
 * @return {string|*}
 */
function truncateWithHtml(string, max, extra = '…'){
    if (typeof string !== "string"){
        string = String(string);
    }
    const trimmedString = string.trimHTML();

    if (trimmedString.length <= max){
        return string;
    }

    // if the string does not contains tags
    if (trimmedString.length === string.length){
        return `<span title="${string}">${string.substring(0, max).trim()}${extra}</span>`;
    }

    // if the string contains a LaTeX formula, don't truncate it
    if (string.includes("math-tex")){
        return string;
    }

    // if the string contains a html button, don't truncate it
    if (string.includes("<button")){
        return string;
    }

    const substrings =  string.split(/(<[^>]*>)/g);

    let count = 0;
    let truncated = [];
    for (let i = 0; i < substrings.length; i++) {
        let substr = substrings[i];
        if (! substr.startsWith("<")){
            if (count > max){
                continue;
            } else if (substr.length > (max-count-1)){
                truncated.push(substr.substring(0, (max-count) - 1) + '…');
            } else {
                truncated.push(substr);
            }
            count += substr.length;
        } else {
            truncated.push(substr);
        }
    }

    return `<span title="${trimmedString}">${truncated.join("")}${extra}</span>`;
}

/**
 * Takes an string of html content and replace the text inside the tags
 * @param htmlString
 * @param textContent
 * @return {string}
 */
function replaceTextFromHtmlString(htmlString, textContent){
    const beginTag = htmlString.substring(0, htmlString.indexOf('>')+1);
    const endTag = htmlString.substring(indexOfNth(htmlString, '<', 2), htmlString.length);
    return `${beginTag}${textContent}${endTag}`;
}

/**
 * This function allows to format correctly two dates in order to display it
 * @param tpq
 * @param taq
 * @return {string}
 */
function getTpaq(tpq, taq) {
    let date;
    if (isDefined(tpq) || isDefined(taq)){
        tpq = isDefined(tpq) ? tpq : "?";
        taq = isDefined(taq) ? taq : "?";

        if ((tpq === taq) && (tpq !== "?")) {
            date = `${tpq}`;
        } else {
            date = `${tpq}–${taq}`;
        }
    } else {
        date = "<span class='noInfo'>?–?</span>";
    }

    return date;
}

/* ---------------------------------- STRING FORMATTING USING TABLE LIST TEMPLATES ---------------------------------- */
/* ----------------------------------- AS USED IN THE HISTORICAL NAVIGATION PAGE ----------------------------------- */
/**
 * @param text
 * @param noInfo
 * @return {*}
 */
const normalizeText = (text, noInfo = "No information provided") => {return isDefined(text) ? text : noInfo;};

/**
 * This function takes a value of node of the result object (results of a query made to ElasticSearch)
 * and the properties associated with the field concerned by this value
 * and returns :
 *  - either the value, if defined
 *  - either a text value indicating a missing information
 */
function formatText(text, properties){
    if (isDefined(text)){
        return text;
    } else {
        if (isDefined(properties) && isDefined(properties.unknown)){
            return `${properties.unknown}`;
        }
        return "No information provided";
    }
}

/**
 * This function takes one result of an object of results from an ajax call to elasticsearch
 * and retrieve the value of the fields that contains the info to display for the title
 * of the specific result in order to return a title as a string
 *
 * @param resultObject : object containing one result of an object of results from an ajax call to elasticsearch
 * @param fieldTemplate : object containing the properties of the field being currently treated
 * @param entity : string of the entity name in snake_case
 * @return {string}
 */
function formatTitle(resultObject, fieldTemplate, entity) {
    // depending on the entity being treated, the node where the information to retrieve is contained is different
    let node = entity === "work" ? resultObject : resultObject.primary_source;
    if (isDefined(node)){
        return `[bold]${formatText(node.default_title, fieldTemplate.properties)}[/] `;
    } else {
        if (typeof fieldTemplate.properties.unknown !== 'undefined'){
            return `${fieldTemplate.properties.unknown}`;
        }
        return "No information provided";
    }
}

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * **
 *                            VARIABLE CHECKING                            *
 ** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * **/
/**
 * to check if a variable is defined and not equal null (value of an empty field in the database), or an empty array
 * @param variable
 * @returns {boolean}
 */
function isDefined (variable){
    if (variable === null || typeof variable === 'undefined'){
        return false;
    } else if (typeof variable === "object" && variable.length === 0) {
        return false;
    }
    return true;
}

const isDef = (variable) => typeof variable !== "undefined";

/**
 * Check if a variable is an array (is written between [])
 * @param a
 * @return boolean
 */
const isArray = function (a) {
    return Array.isArray(a);
};

/**
 * Check if a variable is an object (is written between {})
 * @param o
 * @return {boolean}
 */
const isObject = function (o) {
    return o === Object(o) && !isArray(o) && typeof o !== 'function';
};

/**
 * to check if variable is an array or an object
 * @param variable
 * @returns {boolean}
 */
function isIterable(variable) {
    return isArray(variable) || isObject(variable);
}

/**
 * Checks if an object is empty
 * @param o
 * @return {boolean}
 */
const isObjectEmpty = (o) => {
    return !Object.keys(o).length;
};

/**
 * Order an object by key, alphabetically or numerically
 * @param o
 */
const orderObjectByKey = (o) => {
    var ordered = {};
    Object.keys(o).sort().forEach(function(key) {
        ordered[key] = o[key];
    });
    return ordered;
};

/** Function that count occurrences of a substring in a string;
 * @param {String} string               The string
 * @param {String} subString            The sub string to search for
 * @param {Boolean} [allowOverlapping]  Optional. (Default:false)
 *
 * @author Vitim.us https://gist.github.com/victornpb/7736865
 * @see Unit Test https://jsfiddle.net/Victornpb/5axuh96u/
 * @see http://stackoverflow.com/questions/4009756/how-to-count-string-occurrence-in-string/7924240#7924240
 */
function occurrences(string, subString, allowOverlapping=false) {

    string += "";
    subString += "";
    if (subString.length <= 0) return (string.length + 1);

    var n = 0,
        pos = 0,
        step = allowOverlapping ? 1 : subString.length;

    while (true) {
        pos = string.indexOf(subString, pos);
        if (pos >= 0) {
            ++n;
            pos += step;
        } else break;
    }
    return n;
}

/**
 * Callback Test that can be given as parameter of the filter function in order to returns an array of unique values
 * This test is perform on each value of an array and will returns false if the index of the current value isn't equal
 * to the index of the first time the value is in the array
 *
 * Value correspond to the current value
 * Index correspond to the index of the current value
 * Self correspond to the array being filtered
 * 
 * IndexOf returns the first index of the given value
 *
 * @param value
 * @param index
 * @param self
 * @return {boolean}
 */
const unique = (value, index, self) => {
    return self.indexOf(value) === index;
};

/**
 * This method returns an array containing all the deepest levels
 * of the object given as parameter
 *
 * @example object = [{"actor_name": "Huey"},{"actor_name": {"first_name": "Dewey"}},{"actor_name": "Louie"}];
 * $leaves = getDeepestValues(object);
 * ===> $leaves = ["Huey", "Dewey", "Louie"];
 *
 * @example object = [{"actor_name": {"firstName":"riri", "lastName":"duck"}},{"actor_name": {"firstName":"fifi", "lastName":"duck"}}];
 * $leaves = getDeepestValues(object, [], "firstname");
 * ===> $leaves = ["riri", "fifi"];
 *
 * @param object : object or array
 * @param values
 * @param nodeName : name of the key in which the wanted value is located
 * @returns {Array}
 */
function getDeepestValues(object, values = [], nodeName = null)
{
    for (var node in object){
        if (isIterable(object[node])){
            values = getDeepestValues(object[node], values, nodeName);
        } else {
            if (nodeName === null){
                values.push(object[node]);
            } else {
                if (nodeName === node){ // if we want to select the value corresponding to a specific key
                    values.push(object[node]);
                }
            }
        }
    }
    return values.filter(unique);
}

/**
 * Get minimal value in an array
 * @param a
 * @return {number}
 */
const getMinValueInArray = (a) => {
    return Math.min.apply(null, a);
};

/**
 * Get maximal value in an array
 * @param a
 * @return {number}
 */
const getMaxValueInArray = (a) => {
    return Math.max.apply(null, a);
};

/**
 * Generate the route to a DISHAS record page
 * @param path
 * @param id
 * @return {*}
 */
function generateRoute(path, id){
    id = trimHtmlTags(id);
    return Routing.generate(path, {
        'id': id
    });
}

/**
 * Takes a positive integer and returns the corresponding column name.
 * @param {number} num  The positive integer to convert to a column name.
 * @return {string}  The column name.
 * @see http://cwestblog.com/2013/09/05/javascript-snippet-convert-number-to-column-name/
 */
function toColumnName(num) {
    for (var ret = '', a = 1, b = 26; (num -= a) >= 0; a = b, b *= 26) {
        ret = String.fromCharCode(parseInt((num % b) / a) + 65) + ret;
    }
    return ret;
}

/**
 * This function returns an array of objects ordered by the value (alphabetically and numerically)
 * of the node in each object associated with the key given as parameter
 *
 * Ex : array = [{color: 'white'},{color: 'red'},{color: 'black'}]
 * sortArrayOfObjectsByKey(array, "color") => [{ color: 'black'},{color: 'red'},{color: 'white'}]
 *
 * @param array
 * @param key
 * @return {*}
 */
function sortArrayOfObjectsByKey(array, key) {
    array.sort((a, b) => (a[key] > b[key]) ? 1 : -1);
    return array;
}

/**
 * This function sorts an array according to an object that keys are elements of the array.
 *
 * EXAMPLES :
 * var array = ["id1", "id2", "id3"];
 * var object = {"id1": {"val1": 2, "val2":4}, "id2": {"val1": 1, "val2":3}, "id3": {"val1": 7, "val2":5}};
 *
 * sortArrayAccordingToOtherObject(array, object, "val1") => [id2, id1, id3]..
 *
 * var array = ["id1", "id2", "id3"];
 * var object = {"id1": 66, "id2": 23, "id3": 47};
 *
 * sortArrayAccordingToOtherObject(array, object) => [id2, id3, id1]
 *
 * @param array
 * @param object
 * @param key
 * @return {*}
 */
function sortArrayByObjectProperty(array, object, key=null) {
    if (key){
        const keyOrder = (a, b) => {
            if (typeof object[a] !== "undefined"){
                return (parseInt(object[a][key]) > parseInt(object[b][key])) ? 1 : -1;
            }
        };
        array.sort(keyOrder);
    } else {
        const order = (a, b) => {
            if (typeof object[a] !== "undefined"){
                const a0 = isNaN(parseInt(object[a])) ? object[a] : parseInt(object[a]);
                const b0 = isNaN(parseInt(object[b])) ? object[b] : parseInt(object[b]);
                const a1 = typeof a0, b1 = typeof b0;
                return a1 < b1 ? -1 : a1 > b1 ? 1 : a0 < b0 ? -1 : a0 > b0 ? 1 : 0;
            }
        };
        array.sort(order);
    }
    return array;
}
/**
 * This function returns an array containing all the elements of the first array
 * that are not contained in the second array
 * @param array1
 * @param array2
 * @return {*}
 */
function arrayDiff(array1, array2){
    return array1.filter(function(i) {return array2.indexOf(i) < 0;});
}

/**
 * This function takes an array of object and a key name as parameter
 * and returns an array of all the values corresponding to the key in each object of the array
 *
 * Ex : array = [{color: 'white'},{color: 'red'},{ color: 'black'}]
 * getArrayOfKeyValue(array, "color") => ['black','red','white']
 *
 * @param array
 * @param key
 * @return {*}
 */
function getArrayOfKeyValue(array, key) {
    return array.map(a => a[key]);
}

/**
 * This function takes an html string and puts it in a pop up window
 * @param htmlString
 * @param title
 * @return {Window}
 */
function createPopUp(htmlString, title) {
    let stylesheets = `<link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
                       <link rel="stylesheet" type="text/css" href="/css/table-edition.css">
                       <link rel="stylesheet" type="text/css" href="/css/public.css">
                       <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
                       <link rel="stylesheet" type="text/css" href="/css/tamas.css">`;
    let newWindow = window.open("", "myWindow",'modal=yes,scrollbars=yes');
    newWindow.document.write(`<html lang="en"><head><title>${title}</title>${stylesheets}</head><body><div id="table">${htmlString}</div></body></html>`);

    newWindow.document.close(); // necessary for IE >= 10
    newWindow.focus(); // necessary for IE >= 10

    return newWindow;
}

/**
 * This function returns a duplicate of the object given as parameter
 * @param object
 * @return {object}
 */
function duplicateObject(object) {
    /*return { ...object };*/
    /*return Object.assign({}, object);*/
    return JSON.parse(JSON.stringify(object));
}

/**
 * Get the index of the nth occurrence of a substring in a string
 *
 * @param {string} str : string in which the substring is to be searched
 * @param {string} substr : string to search
 * @param {int} n : int occurrence number of the substring (2 for example for the second time the substring appears in the string)
 * @return {*}
 */
function indexOfNth(str, substr, n){
    var L = str.length, i = -1;
    while(n-- && i++ < L){
        i = str.indexOf(substr, i);
        if (i < 0) break;
    }
    return i;
}

/* ARRAY CONTAINS CHECKING */
/**
 * This function checks if an array contains an object or not
 * @param obj
 * @param arr
 * @return {boolean}
 */
function checkIfArrayContainsObject(obj, arr) {
    for (let i = arr.length - 1; i >= 0; i--) {
        if (isEqual(arr[i], obj)/*arr[i] === obj*/) {
            return true;
        }
    }
    return false;
}

/**
 * check if an array contains a string
 * @param str
 * @param array
 * @return {boolean}
 */
function checkIfArrayContainsString(str, array)
{
    return (array.indexOf(str) > -1);
}

/**
 * This function returns true if the element given as first argument is in the array given as second argument
 * @param element
 * @param array
 * @return {boolean}
 */
function checkIfArrayContains(element, array) {
    if (typeof element === "object"){
        return checkIfArrayContainsObject(element, array);
    } else {
        return checkIfArrayContainsString(element, array);
    }
}

/**
 * capitalize first letter of a string
 * @param str
 * @return {string}
 */
const ucFirst = str => str.charAt(0).toUpperCase() + str.slice(1);

/* -------------------------------------------------------------------- */
/**
 * Adds a content to a key in an object and creates an array in the object associated with the key, in case it doesn't exists
 * @param object : object
 * @param content : value to add to object
 * @param key : string name of the key in which we want to add the content in the object
 * @param addIfAlreadyInArray : boolean determines if the content will be added to the array (associated with the key) if it is already in there
 * @return {*}
 */
function addToObject(object, content, key, addIfAlreadyInArray=true) {
    if (isDefined(object[key])){
        addIfAlreadyInArray ? object[key].push(content): checkIfArrayContains(content, object[key]) ? void(0) : object[key].push(content);
    } else {
        object[key] = [];
        addIfAlreadyInArray ? object[key].push(content): checkIfArrayContains(content, object[key]) ? void(0) : object[key].push(content);
    }
    return object;
}

/**
 * Regex to use to check if there is a number in a string
 *
 * Ex :
 * hasNumber.test("ABC"); // false
 * hasNumber.test("Easy as 123"); // true
 *
 * @type {RegExp}
 */
const hasNumber = /\d/;

/**
 * this function compares two arrays or objects and returns true if they are identical
 * Solution found : https://gomakethings.com/check-if-two-arrays-or-objects-are-equal-with-javascript/
 *
 * @param value
 * @param other
 * @return {boolean}
 */
var isEqual = function (value, other)
{
    // Get the value type
    var type = Object.prototype.toString.call(value);

    // If the two objects are not the same type, return false
    if (type !== Object.prototype.toString.call(other)) return false;

    // If items are not an object or array, return false
    if (['[object Array]', '[object Object]'].indexOf(type) < 0) return false;

    // Compare the length of the length of the two items
    var valueLen = type === '[object Array]' ? value.length : Object.keys(value).length;
    var otherLen = type === '[object Array]' ? other.length : Object.keys(other).length;
    if (valueLen !== otherLen) return false;

    // Compare two items
    var compare = function (item1, item2) {

        // Get the object type
        var itemType = Object.prototype.toString.call(item1);

        // If an object or array, compare recursively
        if (['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
            if (!isEqual(item1, item2)) return false;
        }

        // Otherwise, do a simple comparison
        else {
            // If the two items are not the same type, return false
            if (itemType !== Object.prototype.toString.call(item2)) return false;

            // Else if it's a function, convert to a string and compare
            // Otherwise, just compare
            if (itemType === '[object Function]') {
                if (item1.toString() !== item2.toString()) return false;
            } else {
                if (item1 !== item2) return false;
            }
        }
    };

    // Compare properties
    if (type === '[object Array]') {
        for (var i = 0; i < valueLen; i++) {
            if (compare(value[i], other[i]) === false) return false;
        }
    } else {
        for (var key in value) {
            if (value.hasOwnProperty(key)) {
                if (compare(value[key], other[key]) === false) return false;
            }
        }
    }

    // If nothing failed, return true
    return true;
};

/**
 * Callback Test that can be given as parameter of the filter function in order to returns an array of unique values
 * This test is perform on each value of an array and will returns true is the current value is identical to another in the array,
 * therefore filtering out the value of the array
 *
 * Value correspond to the current value
 * Index correspond to the index of the current value
 * Self correspond to the array being filtered
 *
 * @param value
 * @param index
 * @param self
 * @return {boolean}
 */
const uniqueObject = (value, index, self) => {
    for (var i = 0; i < index; i++) { // i < index in order to not perform the test twice on each value, therefore removing all values that appear twice
        if (isEqual(value, self[i])){
            return false;
        }
    }
    return true;
};

/**
 * This function takes an number and round it up to 5 or 0
 * @param number
 * @param roundUp : number to round up to (5 or 0)
 * @return {number}
 */
function roundUpNumber(number, roundUp) {
    if (parseInt(String(number).slice(-1)) <= 4 || roundUp === 0){
        return parseInt(String(number).substring(0, String(number).length-1)+"0");
    }
    return parseInt(String(number).substring(0, String(number).length-1)+"5");
}

/**
 * This function returns the middle value given an array of numerical values
 * @param arrayOfValues
 * @return {number}
 */
function getMiddleValue(arrayOfValues) {
    return Math.min(...arrayOfValues) + ((Math.max(...arrayOfValues) - Math.min(...arrayOfValues))/2);
}

/**
 * object = {property: {a: {...}, b: {...}, c: {a: "", property: {a: {...}, b: {...}}}}};
 * => {property: [{...}, {...}, {a: "", property: [{...}, {...}]}]};
 * @param object
 * @param property
 * @return {*}
 */
const objectToArrayProperty = (object, property) => {
    if (object.hasOwnProperty(property)){
        object[property] = Object.values(object[property]);
        object[property].map(object => objectToArrayProperty(object, property))
    }
    return object;
};

/**
 * sends a request to the specified url from a form. this will change the window location.
 *
 * @author https://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit
 *
 * @param {string} path the path to send the post request to
 * @param {object} params the parameters to add to the url
 * @param {string} [method=post] the method to use on the form
 */
function post(path, params, method='post') {
    const form = document.createElement('form');
    form.method = method;
    form.action = path;

    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = key;
            hiddenField.value = params[key];

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

/**
 * Returns the coordinates relative to the view port of the DOM element given as parameter
 * @author https://stackoverflow.com/a/26230989
 * @param elem : Element of the DOM
 * @return {{top: number, left: number}}
 */
function getCoords(elem) {
    const box = elem.getBoundingClientRect();

    const body = document.body;
    const docEl = document.documentElement;

    const scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
    const scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;

    const clientTop = docEl.clientTop || body.clientTop || 0;
    const clientLeft = docEl.clientLeft || body.clientLeft || 0;

    const top  = box.top +  scrollTop - clientTop;
    const left = box.left + scrollLeft - clientLeft;

    return { top: Math.round(top), left: Math.round(left) };
}

/**
 * This method generates a downloadable link with the content {content} given as parameter
 * @param {string} content
 * @param {string} fileName 
 * @param {string} format
 */
function downloadFile(content, fileName, format){
    let a = document.createElement('a');
    let header = '';
    if (format === "json"){
        header = `data:text/json;charset=utf-8,`;
    }
    a.setAttribute("href",`${header}${content}`);
    a.setAttribute("download", `${fileName}.${format}`);
    a.click();
    a.remove();
}

/**
 * Create an event trigger on an a element
 * @param id : string id of the html element (a) to which attach the event trigger
 * @param url : string url returning the JSON data to download
 */
function addDownloadEvent(id, url) {
    $(`#${id}`).click(() => {
        downloadJSON(id, url)
    });
}

function downloadJSON(filename, url) {
    $.getJSON(url, response => {
        downloadFile(JSON.stringify(response), filename, 'json');
    });
}

/**
 * const data = { 'first name': 'George', 'last name': 'Jetson', 'age': 110 };
 * const querystring = encodeQueryData(data);
 * @author https://stackoverflow.com/a/111545/11208548
 * @param data
 * @return {string}
 */
function encodeQueryData(data) {
    const ret = [];
    for (let i = 0; i < Object.keys(data).length; i++) {
        const d = Object.keys(data)[i];
        ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
    }
    return ret.join('&');
}

/**
 * Displays in console a nice and friendly error message
 * @param error
 * @param msg
 */
function errorMessage(error, msg="") {
    console.log('%c Error 😰! ', 'font-size: 24px; background: #222; color: #bada55; font-weight: bold');
    console.log(`Line ${error.lineNumber} - ${error.fileName}\n${error}\n${msg}`);
}