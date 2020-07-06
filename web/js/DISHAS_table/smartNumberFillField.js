/* global $ */
/* global nameToBase */
/* global SmartNumber */
/* global SNFromStringBase */


class SmartNumberField {
    /**
     * This method is a shortend to generate the bindings between the smart number field, the original field, the float field and the type of number of a value. 
     * @param {*} smartNumberFieldId 
     * @param {*} floatFieldId 
     * @param {*} origFieldId 
     * @param {*} typeOfNumberFieldId
     * @returns {} dictionnary with key = smartNumberFieldId, value = the bindings.  
     */
    static generateSMFBinds(smartNumberFieldId, floatFieldId, origFieldId, typeOfNumberFieldId){
        let thisField = {};
        thisField.value = new SmartNumberField(smartNumberFieldId);
        thisField.value.bindSelect(typeOfNumberFieldId);
        thisField.value.bindTarget(floatFieldId, 'float');
        thisField.value.bindTarget(origFieldId, undefined, true);
        let dic = {}
        dic[smartNumberFieldId] = thisField;
        return  dic; 
    }

    /**
     * This method is a shortend to fill all the targets of a dictionary of smart number fields. 
     * 
     * @param {*} smartNumberFields 
     */
    static fillAllTargets(smartNumberFields){
        for (let key in smartNumberFields){
            smartNumberFields[key].value.fillTargets();
        }
    }
    constructor(id, errorCallback, errorFinished, originalBase) {
        if(typeof id === "string") {
            this.input = $("#"+id);
        }
        else {
            this.input = id;
        }
        var that = this;
        this.input.change( function(e) {
            that.updateSmartNumber();
        });
        this.smartNumber = undefined;
        this.base = undefined;

        if(originalBase !== undefined) {
            this.base = nameToBase[originalBase];
            if(this.input.val() !== "") {
                this.smartNumber = new SmartNumber(this.input.val(), this.base.name);
            }
        }

        this.targets = [];
        if(errorCallback === undefined) {
            errorCallback = (that) => that.input.closest(".form-group").addClass("has-error-number");
        }
        if(errorFinished === undefined) {
            errorFinished = (that) => that.input.closest(".form-group").removeClass("has-error-number");
        }
        this.errorCallback = errorCallback;
        this.errorFinished = errorFinished;
        this.error = false;

        this.updateCallbacks = [];
    }
    
    empty() {
        this.smartNumber = undefined;
        this.input.val("");
    }
    
    changeBase(base) {
        this.base = base;
        if(this.smartNumber !== undefined) {
            this.smartNumber.computeBase(base.name, 15);
            this.smartNumber.tobases[base.name].removeTrailingZeros();
            this.input.val(this.smartNumber.toStringBase(base.name));
        }
        
        //=> GAlla : Update the number if the base changed. Used in the forms (e.g.: parameter set, mathparam...)  
        this.updateSmartNumber();
    }
    updateSmartNumber() {
        this.error = false;
        //this.input.closest(".form-group").removeClass('has-error');
        if(this.errorFinished !== undefined)
            this.errorFinished(this);
        var string = this.input.val();
        if(string === "") {
            this.smartNumber = undefined;
            return;
        }
        if(this.base !== undefined) {
            try {
                this.smartNumber = SNFromStringBase(string, this.base.name);
                this.input.val(this.smartNumber.toStringBase(this.base.name));
            }
            catch(err) {
                if(err.type === "ParsingError") {
                    console.log("Parsing error");
                    this.smartNumber = undefined;
                    this.error = true;
                    if(this.errorCallback !== undefined) {
                        this.errorCallback(this);
                    }
                    //this.input.closest(".form-group").addClass('has-error');
                }
                else
                    throw(err);
            }
        }
        this.updated();
    }

    onUpdate(func) {
        this.updateCallbacks.push(func);
    }

    updated() {
        this.fillTargets();
        for(var i=0; i<this.updateCallbacks.length; i++)
            this.updateCallbacks[i](this);
    }
    
    bindSelect(id) {
        var select = $("#"+id);
        this.changeBase(nameToBase[select.val()]);
        var that = this;
        select.change(function(e){
            that.changeBase(nameToBase[$(this).val()]);
        });
        this.updateSmartNumber();
    }
    
    bindTarget(id, base, retrieveValue) {
        if(retrieveValue === undefined)
            retrieveValue = false;
        this.targets.push({element: $("#"+id), base: base});
        if(retrieveValue) {
            this.input.val($("#"+id).val());
            this.updateSmartNumber();
        }
        $("#"+id).val("");

    }

    fill(smartNumber) {
        this.smartNumber = smartNumber;
        this.smartNumber.computeBase(this.base.name, 15);
        this.smartNumber.tobases[this.base.name].removeTrailingZeros();
        this.input.val(smartNumber.toStringBase(this.base.name));
    }
    
    fillTargets() {
        for(var i=0; i<this.targets.length; i++) {
            var base = this.targets[i].base;
            if(base === undefined) {
                base = this.base;
            }
            else if(base === "float") {
                base = {name: "none"};
            }
            if(this.smartNumber === undefined) {
                this.targets[i].element.val("");
                continue;
            }
            this.targets[i].element.val(this.smartNumber.toStringBase(base.name));
            this.targets[i].element.change();
        }
    }
}

$('form').submit(function (e) {
    if($('.has-error-number').length > 0) {
        return false;
    }
    return true;
});
