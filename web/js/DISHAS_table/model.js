/* eslint camelcase: "off" */
/* global InputTool */
/* global Fill_1Arg */
/* global Fill_2Arg */
/* global SmartNumber */
/* global SymetricFunction */
/* global AntisymetricFunction */
/* global models */
/* global SolarEquation */
/* global RSine */
/* global RCosine */
/* global SolarDeclination */
/* global RightAscension */
/* global EquationOfTime */
/* global SolarEquationBis */
/* global PlanetaryCenter */

class Parameter {
    /**
     * Class representing an astronomical parameter of a model
     *
     * @example
     * // creates a radius parameter, with a default value of 1.0
     * >> new Parameter("R", {defaultValue: 1.0});
     * 
     * @example
     * // creates 2 linked parameters:
     * // one parameter e
     * // on parameter sin_e
     * >> sin_e = new Parameter("sin_e", {
     *           defaultValue: 0.3995,
     *           linkedParameter: new Parameter("e"),
     *           directTransform: (x) => ((180.0/Math.PI)*Math.asin(x)),
     *           reverseTransform: (x) => (Math.sin(x*Math.PI/180.0))
     *   });
     * >> console.log(sin_e.value);
     * 0.3995
     * >> console.log(sin_e.linkedParameter.name);
     * "e"
     * >> console.log(sin_e.linkedParameter.value);
     * 23.546924786574824
     * 
     * @param  {String} name   Name of the parameter
     * @param  {Object} infos  Additionnal informations about the parameter
     * @return {Parameter}
     */
    constructor(name, infos, latexName) {
        /**
         * Short name of the parameter (e.g. R for radius)
         * @type {String}
         */
        this.name = name;
        /**
         * latexName of the parameter
         * @type {String}
         */
        this.latexName = latexName
        if(this.latexName === undefined) {
            this.latexName = this.name;
        }
        /**
         * If true, the parameter's value can't be changed by the LSQ procedure
         * @type {Boolean}
         */
        this.locked = false;
        if(infos === undefined)
            var infos = {};
        if(infos !== undefined) {
            /**
             * The bounds for this parameter's value. **Can be undefined**
             * @type {Number}
             */
            this.bounds = infos.bounds;
            /**
             * The default value of the parameter
             * @type {Number}
             */
            this.defaultValue = infos.defaultValue;
            if(this.defaultValue === undefined)
                this.defaultValue = 0.0;
            if(this.defaultValue !== undefined)
            /**
             * The current value of this parameter.
             * @type {Number}
             */
                this.value = this.defaultValue;
            /**
             * Another parameter, linked to this one (for e.g. e and cos(e)). **Can be undefined**
             * @type {Parameter}
             */
            this.linkedParameter = infos.linkedParameter;
            if(this.linkedParameter !== undefined) {
                this.linkedParameter.linkedParameter = this;
                /**
                 * The formula to compute the linked parameter from this one. **can be undefined**
                 * @type {function}
                 */
                this.transform = infos.directTransform;
                this.linkedParameter.transform = infos.reverseTransform;
                this.linkedParameter.defaultValue = this.transform(this.defaultValue);
            }
            if(infos.locked !== undefined)
                this.locked = infos.locked;
        }
        if(this.value !== undefined && this.linkedParameter !== undefined)
            this.linkedParameter.value = this.transform(this.value);
    }
}

function processLatexFormula(latexFormula) {
    res = latexFormula;
    res = res.replace(/>([^<]+)\\\)<\/span>/g, '>$1 + C_0\\\)<\/span>');
    res = res.replace(/(\Wx)/g, '($1 + C_x)');
    res = res.replace(/(\Wy)/g, '$1 + C_y');
    return res;
}

class ParametricFunction {
    /**
     * Class representing a parametric function.
     * @example
     * >> function rsin(params, args) {
     *        return params[0]*Math.sin(args[0]*Math.PI/180.0);
     *    }
     * >> function d_rsin_dr(params, args) {
     *        return Math.sin(args[0]*Math.PI/180.0);
     *    }
     * >> function d_rsin_dx(params, args) {
     *        return (Math.PI/180.0)*params[0]*Math.cos(args[0]*Math.PI/180.0);
     *    }
     * >> RSine = new ParametricFunction(
     *        "rsine",
     *        "R sin(x)",
     *        [new Parameter("R", {defaultValue: 1.0})],
     *        1,
     *        rsin,
     *        {
     *             "R": d_rsin_dr,
     *             "x": d_rsin_dx
     *        }
     *    )
     *
     * @example
     * >> function right_ascension(params, args) {
     *        return (180/Math.PI)*Math.atan(Math.tan(args[0]*Math.PI/180.0) * params[0]);
     *    }
     * >> function d_right_ascension_dx(params, args) {
     *        return params[0]*(1+(Math.tan(args[0]*Math.PI/180.0))**2) / (1 + (params[0]*Math.tan(args[0]*Math.PI/180.0))**2);
     *    }
     * >> function d_right_ascension_dcos_e(params, args) {
     *        return (180/Math.PI) * Math.tan(args[0]*Math.PI/180.0) / (1 + (params[0]*Math.tan(args[0]*Math.PI/180.0))**2);
     *    }
     * >> RightAscension = new ParametricFunction(
     *        "rightascension",
     *        "arctan( tan(x) cos_e )",
     *        [new Parameter("cos_e", {
     *            defaultValue: 0.91,
     *            linkedParameter: new Parameter("e"),
     *            directTransform: (x) => ((180.0/Math.PI)*Math.acos(x)),
     *            reverseTransform: (x) => (Math.cos(x*Math.PI/180.0))
     *        })],
     *        1,
     *        right_ascension,
     *        {
     *            "cos_e": d_right_ascension_dcos_e,
     *            "x": d_right_ascension_dx
     *        }
     * 
     * @param  {String}   name            Name of the function
     * @param  {String}   description     String representation of the formula
     * @param  {Object}   parameters      Dictionary of \ :js:class:`Parameter`\ s
     * @param  {Number}   dimension       Number of variables (i.e. # of arguments)
     * @param  {function} jsFunction     The mathematical function
     * @param  {Object}   jsDerivatives  A dictionnary of the derivatives of the function
     * @return {ParametricFunction}
     */
    constructor(name, description, parameters, dimension, jsFunction, jsDerivatives) {
        /**
         * Code name of the function
         * @type {String}
         */
        this.name = name;
        /**
         * Number of variables (i.e. # of arguments)
         * @type {Number}
         */
        this.dimension = dimension;
        /**
         * Dictionary of \ :js:class:`Parameter`\ s
         * @type {Object}
         */
        this.parameters = parameters;
        /**
         * Mathematical function. It must take as an input a list of parameter values and
         * a list of variable values (i.e. argument values), and return the applied function
         * @example
         * // we wish to define R.cos(x) (with x in degrees)
         * >> function rcos(params, args) {
         *        return params[0]*Math.cos(args[0]*Math.PI/180.0);
         *    }
         * // with  params[0] : R  and  args[0] : x in degrees
         * >> console.log(rcos([60.0], [45.0]));
         * 42.42640687119285
         * >> console.log(60 * Math.sqrt(2)/2);
         * 42.42640687119285
         * 
         * @type {function}
         */
        this.jsFunction = jsFunction;
        /**
         * Dictionnary holding the derivatives of the function with respect to each parameter and each variable.
         * 
         * **NB**: The keys in the dictionnary must be the same as the name attribute of each
         * \ :js:class:`Parameter`\ . For the variables (i.e. the arguments), they are called "x", and "y" in \ :js:attr:`dimension`\  2.
         * @example
         * // If we are defining the R.cos(x) parameteric function
         * >> function d_rcos_dr(params, args) {
         *        return Math.cos(args[0]*Math.PI/180.0);
         *    }
         * >> function d_rcos_dx(params, args) {
         *        return -(Math.PI/180.0)*params[0]*Math.sin(args[0]*Math.PI/180.0);
         *    }
         * // the corresponding jsDerivatives would be
         * >> {
         *        "R": d_rcos_dr,
         *        "x": d_rcos_dx
         *    }
         * 
         * 
         * @type {Object}
         */
        this.jsDerivatives = jsDerivatives;
        /**
         * String description of the formula
         * @type {String}
         */
        this.description = description;

        
        /**
         * If true, a new parameter (called "C0") is automatically added, corresponding to a shift in the entry.
         *
         * For now, it is always set to true
         * @type {Boolean}
         */
        this.shifted = true;
        if(this.shifted) {
            this.parameters["C0"] = new Parameter("C0", 
                {defaultValue: 0.0, locked: true},
                "<p><span class=\"math-tex\">\\(C_0\\)</span></p>"
            );
            this.jsDerivatives["C0"] = (a, b) => (1);
            this.jsFunction = (p, a) => (jsFunction(p, a) + p["C0"]);
            this.description += " + C_0";
        }

        /**
         * If true, a new parameter (called "Cx") is automatically added, corresponding to a shift in the first argument.
         * In case of a 2 arguments table, an additional parameter ("Cy") is added.
         *
         * For now, it is always set to true
         * @type {Boolean}
         */
        this.argumentShifted = true;
        if(this.argumentShifted) {
            // shift for x
            this.parameters["Cx"] = new Parameter("Cx",
                {defaultValue: 0.0, locked: true},
                "<p><span class=\"math-tex\">\\(C_x\\)</span></p>"
            );
            var oldJsFunction = this.jsFunction;
            this.jsFunction = (p, a) => {
                var na = []; 
                for(var i=0; i<a.length; i++)
                    na[i] = a[i];
                na[0] += p["Cx"];
                return oldJsFunction(p, na);
            };
            for(var key in this.jsDerivatives) {
                var that = this;
                (function() {
                    var oldJsDerivative = that.jsDerivatives[key];
                    that.jsDerivatives[key] = (p, a) => {
                        var na = [];
                        for(var i=0; i<a.length; i++)
                            na[i] = a[i];
                        na[0] += p["Cx"];
                        return oldJsDerivative(p, na);
                    };
                })();
            }

            if(this.dimension === 2) {
                // shift for y
                this.parameters["Cy"] = new Parameter("Cy",
                    {defaultValue: 0.0, locked: true},
                    "<p><span class=\"math-tex\">\\(C_y\\)</span></p>"
                );
                var oldOldJsFunction = this.jsFunction;
                this.jsFunction = (p, a) => {
                    var na = []; 
                    for(var i=0; i<a.length; i++)
                        na[i] = a[i];
                    na[1] += p["Cy"];
                    return oldOldJsFunction(p, na);
                };
                for(var key in this.jsDerivatives) {
                    var that = this;
                    (function() {
                        var oldOldJsDerivative = that.jsDerivatives[key];
                        that.jsDerivatives[key] = (p, a) => {
                            var na = []; 
                            for(var i=0; i<a.length; i++)
                                na[i] = a[i];
                            na[1] += p["Cy"];
                            return oldOldJsDerivative(p, na);
                        };
                    })();
                }
            }
            
            this.jsDerivatives["Cx"] = (p, a) => this.jsDerivatives["x"](p, a);
            this.jsDerivatives["Cy"] = (p, a) => this.jsDerivatives["y"](p, a);
            
            this.description = this.description.replace(/(\Wx)/g, '$1 + C_x');
            this.description = this.description.replace(/(\Wy)/g, '$1 + C_y');
        }

        /**
         * When defining a \ :js:class:`ParametricFunction`\  object, a corresponding \ :js:class:`InputTool`\   
         * is automatically created.
         * @type {InputTool}
         */
        this.tool = undefined;
        if(this.dimension === 1)
            this.tool = new InputTool(Fill_1Arg, (numbertree) => this.toolHelper(numbertree));
        else
            this.tool = new InputTool(Fill_2Arg, (numbertree) => this.toolHelper(numbertree));
        
        }

    /**
     * Returns a deep copy of the current parameter values of the model
     * @return {Object} Dictionnary of parameter values
     */
    getParameterDictionary() {
        var res = {};
        for(var parameter in this.parameters) {
            res[parameter] = this.parameters[parameter].value;
        }
        return res;
    }

    /**
     * Complete the specified set of parameter values with the values 
     * of the linked parameters
     * @param  {Object} parameters  Dictionary of parameter values
     * @return {undefined}
     */
    completeParameterDictionary(parameters) {
        for(var parameter in parameters) {
            if(!(parameter in this.parameters))
                continue;
            if(this.parameters[parameter].linkedParameter) {
                if(!(this.parameters[parameter].linkedParameter.name in parameters)) {
                    parameters[this.parameters[parameter].linkedParameter.name] = this.parameters[parameter].transform(parameters[parameter]);
                }
            }
        }
    }
    /**
     * Check the specified derivatives of the model by comparing their values
     * with an approximation
     * @return {undefined}
     */
    checkDerivates() {
        var problems = new Set();
        for(var i=0; i<5; i++) {
            var x = i*10.0;
            for(var j=0; j<5; j++) {
                var param = j*0.1;
                var xs = [];
                var ps = {};
                for(var k=0; k<this.dimension; k++)
                    xs.push(x);
                for(var k in this.jsDerivatives)
                    ps[k] = param;
                for(var k in this.jsDerivatives) {
                    var derivate = this.applyDerivate(ps, xs, k);
                    var evaluation = this.approximateDerivate(ps, xs, k);
                    var diff = derivate - evaluation;
                    if(Math.abs(diff) > 0.0001) {
                        problems.add(k);
                        console.log("error in derivate!");
                        console.log(this.name + " wrt " + k);
                        console.log("formula: ", derivate);
                        console.log("approximation: ", evaluation);
                    }
                }
                for(var k=0; k<this.dimension; k++) {
                    var letter = "x";
                    switch(k) {
                    case 0:
                        letter = "x";
                        break;
                    case 1:
                        letter = "y";
                        break;
                    case 2:
                        letter = "z";
                        break;
                    }
                    var derivate = this.applyDerivate(ps, xs, letter);
                    var evaluation = this.approximateDerivate(ps, xs, letter);
                    var diff = derivate - evaluation;
                    if(Math.abs(diff) > 0.0001) {
                        problems.add(letter);
                        console.log("error in derivate!");
                        console.log(this.name + " wrt " + letter);
                        console.log("formula: ", derivate);
                        console.log("approximation: ", evaluation);
                    }
                }
            }
        }
        return problems;
    }

    /**
     * Set the value of a \ :js:class:`Parameter`\  whose name is specified.
     * If the \ :js:class:`Parameter`\  has a \ :js:attr:`linkedParameter`\ , it will update its value too.
     * @param {String} name  Code name of the parameter in the \ :js:attr:`parameterList`\  array
     * @param {Number} value  New value for the parameter
     * @return {undefined}
     */
    setParameterValue(param, value) {
        this.parameters[param].value = value;
        if(this.parameters[param].linkedParameter !== undefined)
            this.parameters[param].linkedParameter.value = this.parameters[param].transform(value);
    }
    
    /**
     * Apply the function, with given values for the parameters and arguments
     * @param  {Array} parameterValues  values for the parameters
     * @param  {Array} argumentValues   values for the arguments
     * @return {Number}
     */
    apply(parameterValues, argumentValues) {
        this.completeParameterDictionary(parameterValues);
        return this.jsFunction(parameterValues, argumentValues);
    }
    /**
     * Compute and return the derivative of the function with respect to the specified parameter, 
     * for given values of the parameters and arguments
     * @param  {Array}  parameterValues  values for the parameters
     * @param  {Array}  argumentValues   values for the arguments
     * @param  {String} parameter        name of the parameter
     * @return {Number}
     */
    applyDerivate(parameterValues, argumentValues, parameter) {
        this.completeParameterDictionary(parameterValues);
        return this.jsDerivatives[parameter](parameterValues, argumentValues);
    }
    /**
     * Compute an approximation [(f(a+delta)-f(a)) / delta] of the derivative of the function with respect to the specified parameter, 
     * for given values of the parameters and arguments.
     *
     * It is used to check if the derivative functions provided by the user are correct.
     * @param  {Array}   parameterValues  values for the parameters
     * @param  {Array}   argumentValues   values for the arguments
     * @param  {String}  parameter        name of the parameter
     * @param  {Number}  [delta=1e-10]
     * @return {Number}
     */
    approximateDerivate(parameterValues, argumentValues, parameter, delta) {
        this.completeParameterDictionary(parameterValues);
        if(delta === undefined)
            var delta = 1e-10;
        var parameterValuesNext = JSON.parse(JSON.stringify(parameterValues));
        var argumentValuesNext = JSON.parse(JSON.stringify(argumentValues));
        if(parameter === "x")
            argumentValuesNext[0] += delta;
        else if(parameter === "y")
            argumentValuesNext[1] += delta;
        else {
            parameterValuesNext[parameter] += delta;
            if(this.parameters[parameter].linkedParameter) {
                parameterValuesNext[this.parameters[parameter].linkedParameter.name] = this.parameters[parameter].transform(parameterValuesNext[parameter]);
            }
        }
        return (this.jsFunction(parameterValuesNext, argumentValuesNext)-this.jsFunction(parameterValues, argumentValues))/delta;
    }
    /**
     * This method correspond to the \ :js:func:`MathFunction`\  used to create an \ :js:class:`InputTool`\  object
     *
     * This is a helper function
     * @param  {Array} numbertree  List of source \ :js:class:`SmartNumber`\ s (here the arguments)
     * @return {SmartNumber}       The target \ :js:class:`SmartNumber`\  (i.e this \ :js:class:`ParametericFunction`\ applied to the provided arguments)
     */
    toolHelper(numbertree) {
        var argumentValues = [];
        for(var i=0; i<numbertree.length; i++) {
            argumentValues.push(numbertree[i].computeDecimal());
        }
        return new SmartNumber(this.apply(this.getParameterDictionary(), argumentValues));
    }
}

class Symetry {
    /**
     * Class representing a symetry.
     * @param  {function} computeNewArgument  function computing the new argument
     * @param  {function} computeNewValue     function computing the new value
     * @param  {function} validity            function for checking if an argument value is legal or not (domain function)
     * @param  {list} parameters                list of parameter's values
     * @return {Symetry}
     */
    constructor(computeNewArgument, computeNewValue, validity, parameters) {
        this.computeNewArgument = computeNewArgument;
        this.computeNewValue = computeNewValue;
        this.parameters = parameters;
        this.validity = validity;
    }
    /**
     * Given an argument value and the entry value at this position, return
     * an argument value and the entry value at this new position, based on this
     * symmetry
     * 
     * @param  {Number} x    considered argument value
     * @param  {Number} f_x  value of the entry at the considered argument
     * @return {Array}    a couple [new_arguments, new_entries], imputed from the symmetry
     */
    imputeNewValue(x, f_x) {
        var possible_new_x = this.computeNewArgument(x, this.parameters);
        var new_x = [];
        var new_fx = [];
        for(var i=0; i<possible_new_x.length; i++) {
            if(this.validity !== undefined && !this.validity(possible_new_x[i], this.parameters))
                continue;
            new_x.push(possible_new_x[i])
            new_f_x.push(this.computeNewValue(f_x, this.parameters));
        }
        return [new_x, new_f_x];
    }
    /**
     * Given an argument value, return a new argument value and a function allowing to
     * compute the entry value at this new position, based on this symmetry
     * @param  {Number} x  considered argument value
     * @return {Array}   a triplet [former_argument, new_arguments, fx_to_newfx_functions], imputed from the symmetry
     */
    imputeNewLink(x) {
        var that = this;
        var possible_new_x = this.computeNewArgument(x, this.parameters);
        var new_x = [];
        for(var i=0; i<possible_new_x.length; i++) {
            if(this.validity !== undefined && !this.validity(possible_new_x[i], this.parameters))
                continue;
            new_x.push(possible_new_x[i])
        }
        if(typeof this.computeNewValue !== "function")
            return [x, new_x, that.computeNewValue];
        return [x, new_x, function(f_x){return that.computeNewValue(f_x, that.parameters);}];
    }
}

/**
 * Create and returns a symmetry object of type Symetry
 * @param {Number} center
 */
function SymetricFunction(center) {
    if(center === undefined)
        var center = 0;
    return new Symetry(
        (x, params) => [2*params[0] - x],
        ["identity"],
        (x) => true,
        [center]
    );
}

/**
 * Create and returns a symmetry object of type Antisymmetry
 * @param {Number} center
 */
function AntisymetricFunction(center) {
    if(center === undefined)
        var center = 0;
    return new Symetry(
        (x, params) => [2*params[0] - x],
        ["opposite"],
        (x) => true,
        [center]
    );
}

/**
 * Create and returns a symmetry object of type Periodic
 * @param {Number} period
 */
function PeriodicFunction(period) {
    if(period === undefined)
        var period = 360;
    return new Symetry(
        function(x, params) {
            var max_window = params[1];
            if(max_window === undefined)
                max_window = 2;
            var res = [];
            // we treat the case i === 1 first (it will allow later to prioritize it)
            res.push(x + params[0]);
            for(var i=-max_window; i<=max_window; i++) {
                if(i === 0)
                    continue;
                if(i === 1)
                    continue;
                res.push(x + i*params[0]);
            }
            return res;
        },
        ["identity"],
        (x) => true,
        [period]
    );
}

/**
 * Create and returns an astronomical symmetry, based on symmetry parameters.
 * The specified parameters can typically be input from the user symmetry form (template or suggest symmetries)
 * @param {String} type          mirror or periodic
 * @param {Number} parameter     center of symmetry or period
 * @param {Number} sign          +1 or -1
 * @param {Number} displacement  displacement value of the function
 */
function AstronomicalSymmetry(type, parameter, sign, displacement) {
    var computeNewArgument = undefined;
    var computeNewValue = undefined;
    if(type === "mirror") {
        computeNewArgument = (x, params) => [2*params[0] - x];
    }
    else if(type === "periodic") {
        computeNewArgument = function(x, params) {
            var max_window = params[1];
            if(max_window === undefined)
                max_window = 1;
            var res = [];
            // we treat the case i === 1 first (it will allow later to prioritize it)
            res.push(x + params[0]);
            for(var i=-max_window; i<=max_window; i++) {
                if(i === 0)
                    continue;
                if(i === 1)
                    continue;
                res.push(x + i*params[0]);
            }
            return res;
        };
    }
    if(sign > 0 && displacement === 0) {
        computeNewValue = ["identity"];
    }
    else if(sign < 0 && displacement === 0) {
        computeNewValue = ["opposite"];
    }
    else if(sign > 0) {
        computeNewValue = ["addition", displacement];
    }
    else if(sign < 0) {
        computeNewValue = ["substraction", displacement];
    }
    return new Symetry(
        computeNewArgument,
        computeNewValue,
        (x) => true,
        [parameter]
    );
}

/**
 * Process the formula input from the formulaDefinition interface, so that
 * it is valid javascript code (replace 'x', 'y', and '$p_i' with args[...] and params[...])
 * @param  {String} text original formula string
 * @return {String}      processed formula string
 */
function processTextFunction(text) {
    var res = "";
    res = text.replace(/([^\w])x([^\w])/g, "$1args[0]$2");
    res = res.replace(/([^\w])x$/g, "$1args[0]");
    res = res.replace(/(^[^\w])x/g, "args[0]$1");
    res = res.replace(/([^\w])y([^\w])/g, "$1args[1]$2");
    res = res.replace(/([^\w])y$/g, "$1args[1]");
    res = res.replace(/(^[^\w])y/g, "args[1]$1");
    res = res.replace(/\$tp_([0-9]*)/g, "params['t' + $1]");
    res = res.replace(/\$p_([0-9]*)/g, "params['o' + $1]");
    return res;
}

/**
 * This function allow to build a Model object, based on the JSON Object
 * build in the formulaDefinition admin interface.
 * @param  {Object} jsonText  a JSON description of a model, as provided by the formulaDefinition admin interface.
 * @return {ParametricFunction}        the corresponding \ :js:func:`ParametricFunction`\  object.
 */
function loadModelJSON(jsonText) {
    var value = jsonText;
    if (typeof jsonText === "string")
        value = JSON.parse(jsonText);
    var mainFormulaText = processTextFunction(value.main_formula);
    var mainFormula = new Function('params', 'args', mainFormulaText);
    var parameters = {};
    for (var parameterCode in value.parameters) {
        if(!value.parameters[parameterCode].direct) {
            parameters['o' + parameterCode.split('_')[1]] = new Parameter(
                'o' + parameterCode.split('_')[1],
                {defaultValue: value.parameters[parameterCode].default},
                value.parameters[parameterCode].latex_name
            )
        }
        else {
            var direct = new Function('x', value.parameters[parameterCode].direct);
            var reverse = new Function('x', value.parameters[parameterCode].reverse);
            parameters['t' + parameterCode.split('_')[1]] = new Parameter(
                't' + parameterCode.split('_')[1],
                {
                    defaultValue: direct(value.parameters[parameterCode].default),
                    linkedParameter: new Parameter(
                        'o' + parameterCode.split('_')[1],
                        {},
                        value.parameters[parameterCode].latex_name
                    ),
                    directTransform: reverse,
                    reverseTransform: direct
                },
                value.parameters[parameterCode].transform_latex_name
            )
            parameters['o' + parameterCode.split('_')[1]] = parameters['t' + parameterCode.split('_')[1]].linkedParameter;
        }
    }


    var derivatives = {};
    for(var parameterCode in value.derivatives) {
        if(parameterCode[0] === '$') {
            if('t' + parameterCode.split('_')[1] in parameters)
                derivatives['t' + parameterCode.split('_')[1]] = new Function('params', 'args', processTextFunction(value.derivatives[parameterCode]));
            else
                derivatives['o' + parameterCode.split('_')[1]] = new Function('params', 'args', processTextFunction(value.derivatives[parameterCode]));
        }
        else
            derivatives[parameterCode] = new Function('params', 'args', processTextFunction(value.derivatives[parameterCode]));
    }


    var dimension = 1;
    if(value.derivatives.y !== undefined)
        dimension = 2;

    var name = "";
    if(value.name !== undefined) {
        name = value.name;
    }
    var model = new ParametricFunction(
        name,
        value.main_formula_latex,
        parameters,
        dimension,
        mainFormula,
        derivatives
    );

    return model;
}


/*
 * ==============================================================
 * Least Squares Block ==========================================
 * ==============================================================
 */

/**
 * Function to compute the Mean Square Error
 * @param {ParametricFunction} model    The \ :js:func:`ParametricFunction`\  to test
 * @param {Array} dataPoints            Array of couples (argValues, entryValue)
 * @return {Number}
 */
function MSE(model, dataPoints) {
    var sum = 0.0;
    var parameterValues = model.getParameterDictionary();
    for(var i=0; i<dataPoints.length; i++) {
        sum += ((dataPoints[i][1] - model.apply(parameterValues, dataPoints[i][0]))**2);
    }
    return sum/dataPoints.length;
}

/**
 * Normalize a vector
 * @param  {Array} vector
 * @return {undefined}
 */
function normalize(vector) {
    var sum = 0.0;
    for(var i in vector) {
        sum += vector[i]**2;
    }
    sum = Math.sqrt(sum);
    if(sum === 0.0)
        return;
    for(var i in vector) {
        vector[i] /= sum;
    }
}

/**
 * Performs a LSQ iteration 
 * @param {ParametricFunction} model   The model to optimize (it contains its parameter values)
 * @param {Array} dataPoints           The dataset to fit. It is an array of couples (argValues, entryValue)
 * @param {Number} epsilon             The gradient's step
 */
function LSQIteration(model, dataPoints, epsilon) {
    var gradient = {};
    var parameterValues = model.getParameterDictionary();
    for(var param in model.jsDerivatives) {
        if(param === 'x'||param === 'y')
            continue;
        if(!(param in model.parameters)) {
            continue;
        }
        gradient[param] = 0.0;
        if(model.parameters[param].locked) {
            continue;
        }
        for(var i=0; i<dataPoints.length; i++) {
            gradient[param] += -2 * (dataPoints[i][1] - model.apply(parameterValues, dataPoints[i][0])) * model.applyDerivate(parameterValues, dataPoints[i][0], model.parameters[param].name);
        }
    }
    stop = true;
    for(var param in gradient) {
        if(gradient[param] !== 0.0) {
            stop = false;
            break;
        }
    }
    if(stop) return [MSE(model, dataPoints), 0.0];
    normalize(gradient);
    
    //backup of the initial value of parameters
    var parameterValuesBak = {};
    for(var param in model.jsDerivatives) {
        if(param === 'x'||param === 'y')
            continue;
        parameterValuesBak[param] = parameterValues[param];
    }
    
    //linear search
    var depthSearch = 10;
    var stepFactor = 10;
    
    var min_i = 0;
    var minMse = Infinity;
    var currentEpsilon = epsilon;
    for(var i=0; i<depthSearch; i++) {
        for(var param in model.jsDerivatives) {
            if(param === 'x'||param === 'y')
                continue;
            if(!(param in model.parameters)) {
                continue;
            }
            model.setParameterValue(param, model.parameters[param].value - currentEpsilon * gradient[param]);
        }
        var mse = MSE(model, dataPoints);
        if(mse < minMse) {
            minMse = mse;
            min_i = i;
        }
        currentEpsilon *= stepFactor;
    }
    var currentEpsilon = epsilon;
    var finalEpsilon = currentEpsilon;
    for(var i=0; i<min_i; i++) {
        currentEpsilon *= stepFactor;
        finalEpsilon += currentEpsilon;
    }
    
    for(var param in model.jsDerivatives) {
        if(param === 'x'||param === 'y')
            continue;
        if(!(param in model.parameters)) {
            continue;
        }
        model.setParameterValue(param, parameterValuesBak[param] - finalEpsilon * gradient[param]);
    }
    
    return [minMse, min_i];
}

/**
 * Least Square procedure.
 * 
 * @param {ParametricFunction} model           \ :js:class:`ParametricFunction`\  on wich we want to perform a parameter estimation
 * @param {Array}              dataPoints      Array of data points [ [x_0, f(x_0)], [x_1, f(x_1)], ..., [x_n, f(x_n)] ]
 * @param {Number}             [epsilon=1e-10] Step for the gradient descent
 * @param {Number}             [nmax=1000]     Max number of iterations
 */
function LSQ(model, dataPoints, epsilon, nmax) {
    if(epsilon === undefined)
        var epsilon = 1e-10;
    if(nmax === undefined) {
        if(model.dimension === 2)
            var nmax = 250;
        else
            var nmax = 1000;
    }
    var prevMse = MSE(model, dataPoints);
    var togo = 10; // number of iteration without improvement before breaking the loop
    
    activateSpinning();

    setTimeout(function(){
        try {
            for(var iteration=0; iteration<nmax; iteration++) {
                var res = LSQIteration(model, dataPoints, epsilon);
                prevMse = res[0];
                if(res[1] === 0) {
                    togo--;
                }
                if(togo <= 0)
                    break;
            }
        }
        finally {
            deactivateSpinning();
            table.parametersToFields();
        }
    }, 100.0);
}

/*
 * =======================================================
 * =======================================================
 */

/**
 * This dictionnary holds all the \ :js:class:`ParametricFunction`\ s available in the interface.
 *
 * Its keys are the name attribute of each \ :js:class:`ParametricFunction`\ .
 * **OBSOLETE** and should be removed if possible
 * @type {Object}
 */
models = {};

/*
 * =====================================================
 * === Below are old definitions of hardcoded models ===
 * === Do not use, but keep them until they are ========
 * === properly entered in the database, via the =======
 * === formulaDefinition admin interface ===============
 * =====================================================
 */

var alpha = 180.0/Math.PI;

// params[0] : e
// args[0] : x, degree
function solar_eq(params, args) {
    return alpha*Math.atan( (params[0]*Math.sin(args[0]/alpha)) / ((60) + (params[0]*Math.cos(args[0]/alpha))) );
}

function d_solar_eq_de(params, args) {
    return (alpha*Math.sin(args[0]/alpha)/60)/(1+2*(params[0]/60)*Math.cos(args[0]/alpha)+(params[0]/60)**2);
}

function d_solar_eq_dx(params, args) {
    var ratio = params[0]/60;
    return (ratio*Math.cos(args[0]/alpha) + ratio**2) / (1 + 2*ratio*Math.cos(args[0]/alpha) + ratio**2)
}
/*
SolarEquation = new ParametricFunction(
    "solarequation",
    "arctan(e sin(x) / (60 + e cos(x)) )",
    [new Parameter("e", {defaultValue: 60})],
    1,
    solar_eq,
    {
        "e": d_solar_eq_de,
        "x": d_solar_eq_dx
    }
);
*/
function solar_eq_bis(params, args) {
    return alpha*Math.asin( (params[0]/60) * Math.sin((args[0] - params[1])/alpha) );
}

function d_solar_eq_bis_de(params, args) {
    return (alpha/60) * Math.sin((args[0] - params[1])/alpha) / (Math.sqrt(1-((params[0]/60)*Math.sin((args[0] - params[1])/alpha))**2));
}

function d_solar_eq_bis_dx(params, args) {
    var ratio = params[0]/60;
    return ratio * Math.cos((args[0] - params[1])/alpha) / (Math.sqrt(1-((params[0]/60)*Math.sin((args[0] - params[1])/alpha))**2));
}
function d_solar_eq_bis_dla(params, args) {
    return -1.0 * d_solar_eq_bis_dx(params, args);
}
/*
SolarEquationBis = new ParametricFunction(
    "solarequationbis",
    "arcsin( e/60 sin(x - la) )",
    [
        new Parameter("e", {defaultValue: 60}),
        new Parameter("la", {defaultValue: 30.0})
    ],
    1,
    solar_eq_bis,
    {
        "e": d_solar_eq_bis_de,
        "la": d_solar_eq_bis_dla,
        "x": d_solar_eq_bis_dx
    }
);
*/
// params[0] : R
// args[0] : x, degree
function rsin(params, args) {
    return params[0]*Math.sin(args[0]*Math.PI/180.0);
}

function d_rsin_dr(params, args) {
    return Math.sin(args[0]*Math.PI/180.0);
}

function d_rsin_dx(params, args) {
    return (Math.PI/180.0)*params[0]*Math.cos(args[0]*Math.PI/180.0);
}
/*
RSine = new ParametricFunction(
    "rsine",
    "R sin(x)",
    [new Parameter("R", {defaultValue: 1.0})],
    1,
    rsin,
    {
        "R": d_rsin_dr,
        "x": d_rsin_dx
    }
);
*/
// params[0] : R
// args[0] : x, degree
function rcos(params, args) {
    return params[0]*Math.cos(args[0]*Math.PI/180.0);
}

function d_rcos_dr(params, args) {
    return Math.cos(args[0]*Math.PI/180.0);
}

function d_rcos_dx(params, args) {
    return -(Math.PI/180.0)*params[0]*Math.sin(args[0]*Math.PI/180.0);
}
/*
RCosine = new ParametricFunction(
    "rcosine",
    "R cos(x)",
    [new Parameter("R", {defaultValue: 1.0})],
    1,
    rcos,
    {
        "R": d_rcos_dr,
        "x": d_rcos_dx
    }
);
*/
// params[0] : sin(eps) -> sin_e
// args[0] : x, degree
function solar_declination(params, args) {
    return (180.0/Math.PI)*Math.asin(Math.sin(args[0]*Math.PI/180.0)*params[0]);
}

function d_solar_declination_dx(params, args) {
    return Math.cos(args[0]*Math.PI/180.0)/Math.sqrt( (1.0/((params[0]**2))) - (Math.sin(args[0]*Math.PI/180.0)**2) );
}

function d_solar_declination_dsin_e(params, args) {
    return (180.0/Math.PI)/Math.sqrt( (1.0/(( Math.sin(args[0]*Math.PI/180.0)  )**2))  -  ((  params[0]  )**2) );
}
/*
SolarDeclination = new ParametricFunction(
    "solardeclination",
    "arcsin( sin(x) sin_e )",
    [new Parameter("sin_e", {
        defaultValue: 0.3995,
        linkedParameter: new Parameter("e"),
        directTransform: (x) => ((180.0/Math.PI)*Math.asin(x)),
        reverseTransform: (x) => (Math.sin(x*Math.PI/180.0))
    })],
    1,
    solar_declination,
    {
        "sin_e": d_solar_declination_dsin_e,
        "x": d_solar_declination_dx
    }
);
*/

// params[0] : cos(eps) -> cos_e
// args[0] : x, degree
function right_ascension(params, args) {
    return (180/Math.PI)*Math.atan(Math.tan(args[0]*Math.PI/180.0) * params[0]);
}

function d_right_ascension_dx(params, args) {
    return params[0]*(1+(Math.tan(args[0]*Math.PI/180.0))**2) / (1 + (params[0]*Math.tan(args[0]*Math.PI/180.0))**2);
    //return 0.0;
    //return Math.cos(args[0])/Math.sqrt( (1.0/((params[0]**2))) - (Math.sin(args[0])**2) );
}

function d_right_ascension_dcos_e(params, args) {
    return (180/Math.PI) * Math.tan(args[0]*Math.PI/180.0) / (1 + (params[0]*Math.tan(args[0]*Math.PI/180.0))**2);
    //return 0.0;
    //return 1.0/Math.sqrt( (1.0/(( Math.sin(args[0])  )**2))  -  ((  params[0]  )**2) );
}
/*
RightAscension = new ParametricFunction(
    "rightascension",
    "arctan( tan(x) cos_e )",
    [new Parameter("cos_e", {
        defaultValue: 0.91,
        linkedParameter: new Parameter("e"),
        directTransform: (x) => ((180.0/Math.PI)*Math.acos(x)),
        reverseTransform: (x) => (Math.cos(x*Math.PI/180.0))
    })],
    1,
    right_ascension,
    {
        "cos_e": d_right_ascension_dcos_e,
        "x": d_right_ascension_dx
    }
);
*/
// cos_eps, e, la, c, D
function equation_of_time(params, args) {
    return (params[4]) * (args[0] + solar_eq_bis([params[1], params[2]], [args[0]]) - right_ascension([params[0]], [args[0]]) + params[3]);
}

function d_equation_of_time_dx(params, args) {
    return (params[4]) * (1 + d_solar_eq_bis_dx([params[1], params[2]], [args[0]]) - d_right_ascension_dx([params[0]], [args[0]]));
}

function d_equation_of_time_dcos_eps(params, args) {
    return -params[4] * d_right_ascension_dcos_e([params[0]], [args[0]]);
}

function d_equation_of_time_de(params, args) {
    return params[4] * d_solar_eq_bis_de([params[1], params[2]], [args[0]]);
}

function d_equation_of_time_dla(params, args) {
    return params[4] * d_solar_eq_bis_dla([params[1], params[2]], [args[0]]);
}

function d_equation_of_time_dc(params, args) {
    return params[4];
}
function d_equation_of_time_dd(params, args) {
    return (args[0] + solar_eq_bis([params[1], params[2]], [args[0]]) - right_ascension([params[0]], [args[0]]) + params[3]);
}
/*
EquationOfTime = new ParametricFunction(
    "equationoftime",
    "(1/D) (x + q(x) - alpha(x) + c)",
    [
        new Parameter("cos_eps", {
            defaultValue: 0.91,
            linkedParameter: new Parameter("eps"),
            directTransform: (x) => ((180.0/Math.PI)*Math.acos(x)),
            reverseTransform: (x) => (Math.cos(x*Math.PI/180.0))
        }),
        new Parameter("e", {defaultValue: 60}),
        new Parameter("la", {defaultValue: 30.0}),
        new Parameter("c", {defaultValue: 1.0}),
        new Parameter("1_d", {
            defaultValue: 1.0,
            linkedParameter: new Parameter("d"),
            directTransform: (x) => (1/x),
            reverseTransform: (x) => (1/x)
        })
    ],
    1,
    equation_of_time,
    {
        "cos_eps": d_equation_of_time_dcos_eps,
        "e": d_equation_of_time_de,
        "la": d_equation_of_time_dla,
        "c": d_equation_of_time_dc,
        "1_d": d_equation_of_time_dd,
        "x": d_equation_of_time_dx
    }
);
*/
//planetary equation of center
function planetary_center(params, args) {
    var ratio = 60/params[0];
    var s = Math.sin(args[0]/alpha);
    var c = Math.cos(args[0]/alpha);
    return alpha * Math.atan((2*s)/(Math.sqrt(ratio**2 - s**2) + c));
}

function d_planetary_center_dx(params, args) {
    var ratio = 60/params[0];
    var s = Math.sin(args[0]/alpha);
    var c = Math.cos(args[0]/alpha);
    var q = Math.sqrt(ratio**2 - s**2);
    return (2*c*(q+c) + 2*(s**2)*((c/q) + 1)) / ((q+c)**2 + 4*s**2);
}

function d_planetary_center_de(params, args) {
    var ratio = 60/params[0];
    var s = Math.sin(args[0]/alpha);
    var c = Math.cos(args[0]/alpha);
    var q = Math.sqrt(ratio**2 - s**2);
    return alpha*(2*(ratio**2)*s/(params[0]*q)) / ((q+c)**2 + 4*s**2);
}

// TODO check
function d_planetary_center_d_60_e(params, args) {
    var ratio = 60/params[0];
    var s = Math.sin(args[0]/alpha);
    var c = Math.cos(args[0]/alpha);
    var q = Math.sqrt(ratio**2 - s**2);
    return -alpha*(2*ratio*s/q) / ((q+c)**2 + 4*s**2);
}
/*
PlanetaryCenter = new ParametricFunction(
    "planetarycenter",
    "arctan( 2 sin(x) / (sqrt((60/e)**2 - sin(x)**2) + cos(x)) )",
    [new Parameter("e", {
        defaultValue: 1.0,
        linkedParameter: new Parameter("60_e"),
        directTransform: (x) => (60/x),
        reverseTransform: (x) => (60/x)
    })],
    1,
    planetary_center,
    {
        "x": d_planetary_center_dx,
        "e": d_planetary_center_de,
        "60_e": d_planetary_center_d_60_e
    }
);
*/


/*
}
models = {
    rsine: RSine,
    solardeclination: SolarDeclination,
    rcosine: RCosine,
    rightascension: RightAscension,
    solarequation: SolarEquation,
    equationoftime: EquationOfTime,
    planetarycenter: PlanetaryCenter
};
*/