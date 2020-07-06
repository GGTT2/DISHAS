/* global SmartNumber */
/* global SuperArea */
/* global InputTool */
/* global flatten */
/* global Get_1Arg_Complete */
/* global Get_2Arg_Complete */
/* global ForwardLinearInterpolation */
/* global BetweenLinearInterpolation */
/* global BetweenArgument */
/* global HorizontalLinearInterpolation */
/* global BackwardLinearInterpolation */
/* global BackwardHorizontalLinearInterpolation */
/* global Identity2Arg */
/* global Identity1Arg */
/* global Diff12Arg */
/* global Diff11Arg */
/* global Diff12ArgHorizontal */
/* global Diff22Arg */
/* global Diff21Arg */
/* global Diff22ArgHorizontal */
/* global Diff1_vertical_classic */
/* global Diff1_vertical_reverse */
/* global Diff1_horizontal_classic */
/* global Diff1_horizontal_reverse */
/* global Diff1_classic */
/* global Diff1_reverse */
/* global Fill_zero_1Arg */
/* global Fill_zero_2Arg */



function insert(element, array) {
    array.push(element);
    array.sort(function(a, b) {
        return a - b;
    });
    return array;
}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

/**
 * An AreaGenerator is any function able to produce source and target \ :js:class:`SuperCell`\ s 
 * (i.e \ :js:class:`SuperArea`\ s) from a selection in a zone.
 * @param {Array} selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], …, ]
 * @param {Array} hots      List of \ :js:class:`RootZone`\ s. in general, if there are 2, the sources are taken from the first, and the
 * target from the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s
 */
function AreaGenerator(selection, hots) {
    return [];
}

/**
 * A MathFunction (in the input tool context) is any function accepting as parameter a tree of \ :js:class:`SmartNumber`\ s 
 * and returning one \ :js:class:`SmartNumber`\ .
 * @param {numberTree} numberTree  a tree of \ :js:class:`SmartNumber`\ s (held by the source \ :js:class:`SuperCell`\ s )
 * @return {SmartNumber}  The result of the mathematical operation, as a \ :js:class:`SmartNumber`\ 
 */
function MathFunction(numberTree) {
    return new SmartNumber(0.0);
}

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a downward interpolation (i.e. classic
 * forward interpolation), based on the specified selection.
 * It is a wrapper around \ :js:class:`VerticalInterpolation_AreaGenerator`\ 
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} [n=1]      The minimal order of the interpolation
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`ForwardInterpolation_builtin`\ s
 */
function ForwardInterpolation_AreaGenerator(selection, hots, n) {
    return VerticalInterpolation_AreaGenerator(selection, hots, n, 1);
}

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a upward interpolation, based on the
 * specified selection.
 * It is a wrapper around \ :js:class:`VerticalInterpolation_AreaGenerator`\ 
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} [n=1]      The minimal order of the interpolation
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`ForwardInterpolation_builtin`\ s
 */
function BackwardInterpolation_AreaGenerator(selection, hots, n) {
    return VerticalInterpolation_AreaGenerator(selection, hots, n, -1);
}

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a vertical forward interpolation, based on the
 * specified selection.
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} [n=1]      The minimal order of the interpolation
 * @param {Number} direction  direction of the interpolation (+1 for downard interpolation, -1 for upward interpolation)
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`ForwardInterpolation_builtin`\ s
 */
function VerticalInterpolation_AreaGenerator(selection, hots, n, direction) {
    if(n === undefined)
        var n=1;
    n++;
    var hot = hots[0];
    if(selection === undefined)
        var selection = hot.getSelectedCells();
    
    if(selection.length === 0)
        return new SuperArea([],[]);
    
    var SuperCells = hot.selectionToSuperCells(selection);
    
    if(SuperCells.length === 1) {
        //use memory
    }
    
    var arg0Down = 0;
    var arg0Up = Infinity;
    var arg0AllEntry = true;
    var arg0IsEntry = false;
    var arg0List = [];
    var arg1List = [];
    for(var i=0; i<SuperCells.length; i++) {
        insert(SuperCells[i].infos.argPos0, arg0List);
        if(hot.nargs === 2)
            insert(SuperCells[i].infos.argPos1, arg1List);
        if(SuperCells[i].infos.argPos0 > arg0Down) {
            arg0Down = SuperCells[i].infos.argPos0;
        }
        if(SuperCells[i].infos.argPos0 < arg0Up) {
            arg0Up = SuperCells[i].infos.argPos0;
        }
        if(SuperCells[i].infos.argPos0 === arg0Down) {
            if(!SuperCells[i].isEntry() /*&& !SuperCells[i].testFullProp("suggested", false)*/)
                arg0AllEntry = false;
            if(SuperCells[i].isEntry())
                arg0IsEntry = true;
        }
    }
    
    if(!arg0AllEntry)
        return [];
    
    arg0List = arg0List.filter(onlyUnique);
    arg1List = arg1List.filter(onlyUnique);
    
    var argTarget;
    var offset = 1;
    if(!arg0IsEntry) {
        if(direction > 0)
            argTarget = arg0Down;
        else
            argTarget = arg0Up;
    }
    else {
        if(direction > 0)
            var ref = arg0Down;
        else
            var ref = arg0Up;
        if(arg0AllEntry) {
            argTarget = ref + direction;
            offset = 0;
        }
        else {
            argTarget = ref;
        }
    }
    
    
    var argPosTarget = [argTarget];
    if(hot.nargs === 2)
        argPosTarget.push(arg1List[0]);
    
    var SCNewArg = hot.selectedMetaZone.fromPath(flatten([1,0,argPosTarget]));
    var SCTarget = hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget]));
    
    //now we seek n args and their corresponding entries
    //first in the selection
    var SCNArgs = [];
    var SCNEntries = [];
    if(direction > 0) {
        for(var i=arg0List.length -1 -offset; i>=0; i--) {
            var argPosTarget = [arg0List[i]];
            if(hot.nargs === 2)
                argPosTarget.push(arg1List[0]);
            SCNArgs.push(hot.selectedMetaZone.fromPath(flatten([1,0,argPosTarget])));
            SCNEntries.push(hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget])));
        }
    }
    else {
        for(var i=0; i<=arg0List.length -1 -offset; i++) {
            var argPosTarget = [arg0List[i]];
            if(hot.nargs === 2)
                argPosTarget.push(arg1List[0]);
            SCNArgs.push(hot.selectedMetaZone.fromPath(flatten([1,0,argPosTarget])));
            SCNEntries.push(hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget])));
        }
    }
    //then in the remaining of the gridXY
    var num = n - SCNEntries.length;
    if(num > 0) {
        if(SCNEntries.length > 0)
            var last = SCNEntries[SCNEntries.length-1];
        else
            var last = SCTarget;
        var ups = last.nextNArgs(0,-direction,num);
        for(var i=0; i<ups.length; i++) {
            if(hot.nargs === 1)
                SCNArgs.push(hot.selectedMetaZone.fromPath([1,0,ups[i].infos.argPos0]));
            else {
                SCNArgs.push(hot.selectedMetaZone.fromPath([1,0,ups[i].infos.argPos0,0]));
            }
        }
        SCNEntries = SCNEntries.concat(ups);
    }
    if(SCNEntries.length < 2) {
        var newEntry = SCNEntries[0].nextNArgs(0,-direction,1,false);
        SCNEntries.push(newEntry[0]);
        if(hot.nargs === 1)
            SCNArgs.push(hot.selectedMetaZone.fromPath([1,0,newEntry[0].infos.argPos0]));
        else {
            SCNArgs.push(hot.selectedMetaZone.fromPath([1,0,newEntry[0].infos.argPos0,0]));
        }
    }
    if(SCNArgs.length < n || SCNEntries.length < n)
        return [];

    return [new SuperArea([SCNArgs, SCNEntries, SCNewArg],SCTarget)];
}

function evalLagrange(args, index, x) {
    var acc = 1.0;
    for(var i=0; i<args.length; i++) {
        if(i === index)
            continue;
        acc *= (((x.computeDecimal() - args[i].computeDecimal())) / (args[index].computeDecimal() - args[i].computeDecimal()));
    }
    return acc;
}

/**
 * Mathematical function to compute an order-n interpolation (thanks to Lagrange interpolation)
 * @param {Array} numberTree  a tree of \ :js:class:`SmartNumber`\ s
 * @return {SmartNumber}
 */
function ForwardInterpolation_builtin(numberTree) {
    var nArgs = numberTree[0];
    var nEntries = numberTree[1];
    var newArg = numberTree[2];
    
    //les n polynomes de Lagrange evalues en newArg
    var lagranges = [];
    for(var i=0; i<nArgs.length; i++) {
        lagranges.push(evalLagrange(nArgs, i, newArg));
    }
    
    var newVal = 0.0;
    for(var i=0; i<nArgs.length; i++) {
        newVal += nEntries[i].computeDecimal() * lagranges[i];
    }
    
    return new SmartNumber(newVal);
}

/**
 * Tool for performing vertical forward interpolation
 * @type {InputTool}
 */
ForwardLinearInterpolation = new InputTool(ForwardInterpolation_AreaGenerator, ForwardInterpolation_builtin);
/**
 * Tool for performing vertical backward interpolation
 * @type {InputTool}
 */
BackwardLinearInterpolation = new InputTool(BackwardInterpolation_AreaGenerator, ForwardInterpolation_builtin);

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a rightward interpolation, based on the
 * specified selection.
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} [n=1]      The minimal order of the interpolation
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`ForwardInterpolation_builtin`\ s
 */
function HorizontalForwardInterpolation_AreaGenerator(selection, hots, n) {
    return HorizontalInterpolation_AreaGenerator(selection, hots, n, 1);
}

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a leftward interpolation, based on the
 * specified selection.
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} [n=1]      The minimal order of the interpolation
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`ForwardInterpolation_builtin`\ s
 */
function HorizontalBackwardInterpolation_AreaGenerator(selection, hots, n) {
    return HorizontalInterpolation_AreaGenerator(selection, hots, n, -1);
}

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a horizontal forward interpolation, based on the
 * specified selection.
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} [n=1]      The minimal order of the interpolation
 * @param {Number} direction  direction of the interpolation (+1 for rightward interpolation, -1 for leftward interpolation)
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`ForwardInterpolation_builtin`\ s
 */
function HorizontalInterpolation_AreaGenerator(selection, hots, n, direction) {
    if(n === undefined)
        var n=1;
    n++;
    var hot = hots[0];
    if(selection === undefined)
        var selection = hot.getSelectedCells();
    
    if(selection.length === 0)
        return [];
    
    var SuperCells = hot.selectionToSuperCells(selection);
    
    var arg1Down = 0;
    var arg1Up = Infinity;
    var arg1AllEntry = true;
    var arg1IsEntry = false;
    var arg1List = [];
    var arg0List = [];
    for(var i=0; i<SuperCells.length; i++) {
        insert(SuperCells[i].infos.argPos1, arg1List);
        insert(SuperCells[i].infos.argPos0, arg0List);
        if(SuperCells[i].infos.argPos1 > arg1Down) {
            arg1Down = SuperCells[i].infos.argPos1;
        }
        if(SuperCells[i].infos.argPos1 < arg1Up) {
            arg1Up = SuperCells[i].infos.argPos1;
        }
        if(SuperCells[i].infos.argPos1 === arg1Down) {
            if(!SuperCells[i].isEntry()/* && !SuperCells[i].testFullProp("suggested", false)*/)
                arg1AllEntry = false;
            if(SuperCells[i].isEntry())
                arg1IsEntry = true;
        }
    }
    arg0List = arg0List.filter(onlyUnique);
    arg1List = arg1List.filter(onlyUnique);
    
    var argTarget;
    var offset = 1;
    if(!arg1IsEntry) {
        if(direction > 0)
            argTarget = arg1Down;
        else
            argTarget = arg1Up;
    }
    else {
        if(direction > 0)
            var ref = arg1Down;
        else
            var ref = arg1Up;
        if(arg1AllEntry) {
            argTarget = ref + direction;
            offset = 0;
        }
        else {
            argTarget = ref;
        }
    }
    
    
    var argPosTarget = [arg0List[0],argTarget];
    
    var SCNewArg = hot.selectedMetaZone.fromPath(flatten([1,1,argPosTarget]));
    var SCTarget = hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget]));
    
    //now we seek n args and their corresponding entries
    //first in the selection
    var SCNArgs = [];
    var SCNEntries = [];
    if(direction > 0)
        for(var i=arg1List.length -1 -offset; i>=0; i--) {
            var argPosTarget = [arg0List[0], arg1List[i]];
            SCNArgs.push(hot.selectedMetaZone.fromPath(flatten([1,1,argPosTarget])));
            SCNEntries.push(hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget])));
        }
    else
        for(var i=0; i<=arg1List.length -1 -offset; i++) {
            var argPosTarget = [arg0List[0], arg1List[i]];
            SCNArgs.push(hot.selectedMetaZone.fromPath(flatten([1,1,argPosTarget])));
            SCNEntries.push(hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget])));
        }
    //then in the remaining of the gridXY
    var num = n - SCNEntries.length;
    if(num > 0) {
        if(SCNEntries.length > 0)
            var last = SCNEntries[SCNEntries.length-1];
        else
            var last = SCTarget;
        var ups = last.nextNArgs(1,-direction,num);
        for(var i=0; i<ups.length; i++) {
            SCNArgs.push(hot.selectedMetaZone.fromPath([1,1,0,ups[i].infos.argPos1]));
        }
        SCNEntries = SCNEntries.concat(ups);
    }

    if(SCNEntries.length < 2) {
        var newEntry = SCNEntries[0].nextNArgs(1,-direction,1,false);
        SCNEntries.push(newEntry[0]);
        SCNArgs.push(hot.selectedMetaZone.fromPath([1,1,newEntry[0].infos.argPos0,newEntry[0].infos.argPos1]));
    }

    if(SCNArgs.length < n || SCNEntries.length < n)
        return [];

    return [new SuperArea([SCNArgs, SCNEntries, SCNewArg],SCTarget)];
}

/**
 * Tool for performing forward horizontal interpolation
 * @type {InputTool}
 */
HorizontalLinearInterpolation = new InputTool(HorizontalForwardInterpolation_AreaGenerator, ForwardInterpolation_builtin);
/**
 * Tool for performing backward horizontal interpolation
 * @type {InputTool}
 */
BackwardHorizontalLinearInterpolation = new InputTool(HorizontalBackwardInterpolation_AreaGenerator, ForwardInterpolation_builtin);

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a linear in-between interpolation.
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} n          DEPRECATED
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`ForwardInterpolation_builtin`\ s
 */
function Between_AreaGenerator(selection, hots, n) {
    
    //TODO biliear shortcut
    var hot = hots[0];
    
    var SuperCells = hot.selectionToSuperCells(selection);
    var areas = [];
    var byArg1 = {};
    var byArg0 = {};
    for(var i=0; i<SuperCells.length; i++) {
        var sc = SuperCells[i];
        if(!sc.testFullProp("suggested", false) || sc.isArgument())
            continue;
        if(hot.nargs === 2)
            var argpos1 = sc.infos.argPos1;
        else
            var argpos1 = 0;
        if(argpos1 in byArg1)
            insert(sc.infos.argPos0, byArg1[argpos1]);
        else
            byArg1[argpos1] = [sc.infos.argPos0];
        if(hot.nargs === 2) {
            var argpos0 = sc.infos.argPos0;
            if(argpos0 in byArg0)
                insert(sc.infos.argPos1, byArg0[argpos0]);
            else
                byArg0[argpos0] = [sc.infos.argPos1];
        }
        
    }
    for(var key in byArg1) {
        for(var i=0; i<byArg1[key].length - 1; i++) {
            if(hot.nargs === 2) {
                var argPos0 = [byArg1[key][i], Number(key)];
                var argPos1 = [byArg1[key][i+1], Number(key)];
                var argPosTarget = [byArg1[key][i], Number(key)];
            }
            else {
                var argPos0 = [byArg1[key][i]];
                var argPos1 = [byArg1[key][i+1]];
                var argPosTarget = [byArg1[key][i]];
            }
            
            var sc0 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos0]));
            var sc1 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos1]));
            var arg0 = hot.selectedMetaZone.fromPath(flatten([1,0,argPos0]));
            var arg1 = hot.selectedMetaZone.fromPath(flatten([1,0,argPos1]));
            for(var k=argPos0[0]+1; k<argPos1[0]; k++) {
                argPosTarget[0] = k;
                var narg =  hot.selectedMetaZone.fromPath(flatten([1,0,argPosTarget]));
                var nentry = hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget]));
                areas.push(new SuperArea([[arg0, arg1],[sc0, sc1],narg], nentry));
            }
        }
    }
    if(hot.nargs === 2) {
        for(var key in byArg0) {
            for(var i=0; i<byArg0[key].length - 1; i++) {
                var argPos0 = [Number(key), byArg0[key][i]];
                var argPos1 = [Number(key), byArg0[key][i+1]];
                var argPosTarget = [Number(key), byArg0[key][i]];

                var sc0 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos0]));
                var sc1 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos1]));
                var arg0 = hot.selectedMetaZone.fromPath(flatten([1,1,argPos0]));
                var arg1 = hot.selectedMetaZone.fromPath(flatten([1,1,argPos1]));
                for(var k=argPos0[1]+1; k<argPos1[1]; k++) {
                    argPosTarget[1] = k;
                    var narg =  hot.selectedMetaZone.fromPath(flatten([1,1,argPosTarget]));
                    var nentry = hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget]));
                    areas.push(new SuperArea([[arg0, arg1],[sc0, sc1],narg], nentry));
                }
            }
        }
    }
    return areas;
}

/**
 * Tool for filling between 2 entry values, with linear interpolation
 * @type {InputTool}
 */
BetweenLinearInterpolation = new InputTool(Between_AreaGenerator, ForwardInterpolation_builtin);

/**
 * Function to generate the source and target \ :js:class:`SuperCell`\ s of a constant step argument interpolation.
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. If there are 2, the sources are taken from the first, and the
 * target from the second
 * @param {Number} n          DEPRECATED
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`StepInterpolation_builtin`\ s
 */
function BetweenArgument_AreaGenerator(selection, hots, n) {
    
    //TODO biliear shortcut
    var hot = hots[0];
    
    var SuperCells = hot.selectionToSuperCells(selection);
    var areas = [];
    var byArg1 = {};
    var byArg0 = {};
    for(var i=0; i<SuperCells.length; i++) {
        var sc = SuperCells[i];
        if(!sc.testFullProp("suggested", false) || !sc.isArgument())
            continue;
        if(hot.nargs === 2)
            var argpos1 = sc.infos.argPos1;
        else
            var argpos1 = 0;
        if(argpos1 in byArg1)
            insert(sc.infos.argPos0, byArg1[argpos1]);
        else
            byArg1[argpos1] = [sc.infos.argPos0];
        if(hot.nargs === 2) {
            var argpos0 = sc.infos.argPos0;
            if(argpos0 in byArg0)
                insert(sc.infos.argPos1, byArg0[argpos0]);
            else
                byArg0[argpos0] = [sc.infos.argPos1];
        }
        
    }
    for(var key in byArg1) {
        for(var i=0; i<byArg1[key].length - 1; i++) {
            if(hot.nargs === 2) {
                var argPos0 = [byArg1[key][i], Number(key)];
                var argPos1 = [byArg1[key][i+1], Number(key)];
                var argPosTarget = [byArg1[key][i], Number(key)];
            }
            else {
                var argPos0 = [byArg1[key][i]];
                var argPos1 = [byArg1[key][i+1]];
                var argPosTarget = [byArg1[key][i]];
            }
            
            //var sc0 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos0]));
            //var sc1 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos1]));
            var arg0 = hot.selectedMetaZone.fromPath(flatten([1,0,argPos0]));
            var arg1 = hot.selectedMetaZone.fromPath(flatten([1,0,argPos1]));
            for(var k=argPos0[0]+1; k<argPos1[0]; k++) {
                argPosTarget[0] = k;
                var narg =  hot.selectedMetaZone.fromPath(flatten([1,0,argPosTarget]));
                //var nentry = hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget]));
                areas.push(new SuperArea([arg0, arg1], narg, [k-(argPos0[0]+1), argPos1[0]-(argPos0[0]+1)]));
            }
        }
    }
    if(hot.nargs === 2) {
        for(var key in byArg0) {
            for(var i=0; i<byArg0[key].length - 1; i++) {
                var argPos0 = [Number(key), byArg0[key][i]];
                var argPos1 = [Number(key), byArg0[key][i+1]];
                var argPosTarget = [Number(key), byArg0[key][i]];

                //var sc0 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos0]));
                //var sc1 = hot.selectedMetaZone.fromPath(flatten([0,0,argPos1]));
                var arg0 = hot.selectedMetaZone.fromPath(flatten([1,1,argPos0]));
                var arg1 = hot.selectedMetaZone.fromPath(flatten([1,1,argPos1]));
                for(var k=argPos0[1]+1; k<argPos1[1]; k++) {
                    argPosTarget[1] = k;
                    var narg =  hot.selectedMetaZone.fromPath(flatten([1,1,argPosTarget]));
                    //var nentry = hot.selectedMetaZone.fromPath(flatten([0,0,argPosTarget]));
                    areas.push(new SuperArea([arg0, arg1], narg, [k-(argPos0[1]+1), argPos1[1]-(argPos0[1]+1)]));
                }
            }
        }
    }
    return areas;
}

/**
 * Mathematical function to compute the value between two \ :js:class:`SmartNumber`\ s, assuming a constant step between
 * those two values.
 * @param {Array}  numberTree  a tree of \ :js:class:`SmartNumber`\ s
 * @param {Array}  options     list containing [index, nsteps], where nmax is the number of \ :js:class:`SuperCell`\ s separing the 2 \ :js:class:`SuperCell`\ s,
 * and index is the index of the target \ :js:class:`SuperCell`\ s whose value we are computing.
 * @return {SmartNumber}
 */
function StepInterpolation_builtin(numbertree, options) {
    var step = (numbertree[1].computeDecimal() - numbertree[0].computeDecimal())/(options[1]+1);
    var newval = numbertree[0].computeDecimal() + (options[0]+1)*step;
    return new SmartNumber(newval);
}

/**
 * Tool for filling between 2 arguments with a constant step
 * @type {InputTool}
 */
BetweenArgument = new InputTool(BetweenArgument_AreaGenerator, StepInterpolation_builtin);

function Identity2Arg_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var cells = hots[0].selectionToSuperCells(selection);
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    return [new SuperArea(cells[0], hots[1].fromPath([0,0,cells[0].infos.argPos0, cells[0].infos.argPos1]))];
}

function Identity1Arg_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var cells = hots[0].selectionToSuperCells(selection);
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    return [new SuperArea(cells[0], hots[1].fromPath([0,0,cells[0].infos.argPos0]))];
}

function Identity_builtin(numbertree) {
    return numbertree;
}

Identity2Arg = new InputTool(Identity2Arg_AreaGenerator, Identity_builtin);
Identity1Arg = new InputTool(Identity1Arg_AreaGenerator, Identity_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s based on a selection for the computation of vertical 1st differences,
 * in the case of a 2-arguments table
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The sources are extracted from the first, and the target from the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff12Arg_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    if(typeof(selection[0][0]) === "number")
        var cells = hots[0].selectionToSuperCells(selection);
    else {
        var cells = selection;
    }
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    var res = [];
    if(cells[0].infos.argPos0-1 >= 0)
        res.push(new SuperArea([cells[0], cells[0].nextArg(0,-1)], hots[1].fromPath([0,0,cells[0].infos.argPos0,cells[0].infos.argPos1])));
    if(cells[0].infos.argPos0+1 < hots[0].spec.args[0].nsteps)
        res.push(new SuperArea([cells[0].nextArg(0,1), cells[0]], hots[1].fromPath([0,0,cells[0].infos.argPos0 + 1,cells[0].infos.argPos1])));
    return res;
}
/**
 * Generate the source and target \ :js:class:`SuperCell`\ s based on a selection for the computation of horizontal 1st differences,
 * in the case of a 2-arguments table
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The sources are extracted from the first, and the target from the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff12Arg_Horizontal_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    if(typeof(selection[0][0]) === "number")
        var cells = hots[0].selectionToSuperCells(selection);
    else {
        var cells = selection;
    }
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    var res = [];
    if(cells[0].infos.argPos1-1 >= 0)
        res.push(new SuperArea([cells[0], cells[0].nextArg(1,-1)], hots[1].fromPath([0,0,cells[0].infos.argPos0,cells[0].infos.argPos1])));
    if(cells[0].infos.argPos1+1 < hots[0].spec.args[1].nsteps)
        res.push(new SuperArea([cells[0].nextArg(1,1), cells[0]], hots[1].fromPath([0,0,cells[0].infos.argPos0,cells[0].infos.argPos1+1])));
    return res;
}
/**
 * Generate the source and target \ :js:class:`SuperCell`\ s based on a selection for the computation of vertical 1st differences,
 * in the case of a 1-arguments table
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The sources are extracted from the first, and the target from the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff11Arg_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    if(typeof(selection[0][0]) === "number")
        var cells = hots[0].selectionToSuperCells(selection);
    else {
        var cells = selection;
    }
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    var res = [];
    if(cells[0].infos.argPos0-1 >= 0)
        res.push(new SuperArea([cells[0], cells[0].nextArg(0,-1)], hots[1].fromPath([0,0,cells[0].infos.argPos0])));
    if(cells[0].infos.argPos0+1 < hots[0].spec.args[0].nsteps)
        res.push(new SuperArea([cells[0].nextArg(0,1), cells[0]], hots[1].fromPath([0,0,cells[0].infos.argPos0 + 1])));
    return res;
}

/**
 * Mathematical function to compute the 1st differences between two \ :js:class:`SmartNumber`\ s
 * @param {Array}  numberTree  a tree of \ :js:class:`SmartNumber`\ s
 * @return {SmartNumber}
 */
function Diff1_builtin(numbertree) {
    return new SmartNumber(numbertree[0].computeDecimal() - numbertree[1].computeDecimal());
}

/**
 * Tool for computing the vertical 1st differences in a 2-arguments table
 * @type {InputTool}
 */
Diff12Arg = new InputTool(Diff12Arg_AreaGenerator, Diff1_builtin);
/**
 * Tool for computing the 1st differences in a 1-arguments table
 * @type {InputTool}
 */
Diff11Arg = new InputTool(Diff11Arg_AreaGenerator, Diff1_builtin);
/**
 * Tool for computing the horizontal 1st differences in a 2-arguments table
 * @type {InputTool}
 */
Diff12ArgHorizontal = new InputTool(Diff12Arg_Horizontal_AreaGenerator, Diff1_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s based on a selection for the computation of vertical 2nd differences,
 * in the case of a 2-arguments table
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The sources are extracted from the first, and the target from the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff2_builtin`\ s
 */
function Diff22Arg_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    if(typeof(selection[0][0]) === "number")
        var cells = hots[0].selectionToSuperCells(selection);
    else
        var cells = selection;
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    var res = [];
    if(cells[0].infos.argPos0-2 >= 0)
        res.push(new SuperArea([cells[0], cells[0].nextArg(0,-1), cells[0].nextArg(0,-2)], hots[1].fromPath([0,0,cells[0].infos.argPos0,cells[0].infos.argPos1])));
    if(cells[0].infos.argPos0+1 < hots[0].spec.args[0].nsteps && cells[0].infos.argPos0-1 >= 0)
        res.push(new SuperArea([cells[0].nextArg(0,1), cells[0], cells[0].nextArg(0,-1)], hots[1].fromPath([0,0,cells[0].infos.argPos0+1,cells[0].infos.argPos1])));
    if(cells[0].infos.argPos0+2 < hots[0].spec.args[0].nsteps)
        res.push(new SuperArea([cells[0].nextArg(0,2), cells[0].nextArg(0,1), cells[0]], hots[1].fromPath([0,0,cells[0].infos.argPos0+2,cells[0].infos.argPos1])));
    return res;
}
/**
 * Generate the source and target \ :js:class:`SuperCell`\ s based on a selection for the computation of Horizontal 2nd differences,
 * in the case of a 2-arguments table
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The sources are extracted from the first, and the target from the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff2_builtin`\ s
 */
function Diff22Arg_Horizontal_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    if(typeof(selection[0][0]) === "number")
        var cells = hots[0].selectionToSuperCells(selection);
    else
        var cells = selection;
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    var res = [];
    if(cells[0].infos.argPos1-2 >= 0)
        res.push(new SuperArea([cells[0], cells[0].nextArg(1,-1), cells[0].nextArg(1,-2)], hots[1].fromPath([0,0,cells[0].infos.argPos0,cells[0].infos.argPos1])));
    if(cells[0].infos.argPos1+1 < hots[0].spec.args[1].nsteps && cells[0].infos.argPos1-1 >= 0)
        res.push(new SuperArea([cells[0].nextArg(1,1), cells[0], cells[0].nextArg(1,-1)], hots[1].fromPath([0,0,cells[0].infos.argPos0,cells[0].infos.argPos1+1])));
    if(cells[0].infos.argPos1+2 < hots[0].spec.args[1].nsteps)
        res.push(new SuperArea([cells[0].nextArg(1,2), cells[0].nextArg(1,1), cells[0]], hots[1].fromPath([0,0,cells[0].infos.argPos0,cells[0].infos.argPos1+2])));
    return res;
}
/**
 * Generate the source and target \ :js:class:`SuperCell`\ s based on a selection for the computation of vertical 2nd differences,
 * in the case of a 1-arguments table
 * @param {Array}  selection  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The sources are extracted from the first, and the target from the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff2_builtin`\ s
 */
function Diff21Arg_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    if(typeof(selection[0][0]) === "number")
        var cells = hots[0].selectionToSuperCells(selection);
    else
        var cells = selection;
    if(cells.length === 0 || !cells[0].isEntry())
        return [];
    var res = [];
    if(cells[0].infos.argPos0-2 >= 0)
        res.push(new SuperArea([cells[0], cells[0].nextArg(0,-1), cells[0].nextArg(0,-2)], hots[1].fromPath([0,0,cells[0].infos.argPos0])));
    if(cells[0].infos.argPos0+1 < hots[0].spec.args[0].nsteps && cells[0].infos.argPos0-1 >= 0)
        res.push(new SuperArea([cells[0].nextArg(0,1), cells[0], cells[0].nextArg(0,-1)], hots[1].fromPath([0,0,cells[0].infos.argPos0+1])));
    if(cells[0].infos.argPos0+2 < hots[0].spec.args[0].nsteps)
        res.push(new SuperArea([cells[0].nextArg(0,2), cells[0].nextArg(0,1), cells[0]], hots[1].fromPath([0,0,cells[0].infos.argPos0+2])));
    return res;
}
/**
 * Mathematical function to compute the 2nd differences of three \ :js:class:`SmartNumber`\ s
 * @param {Array}  numberTree  a tree of \ :js:class:`SmartNumber`\ s
 * @return {SmartNumber}
 */
function Diff2_builtin(numbertree) {
    return new SmartNumber(numbertree[0].computeDecimal() - 2*numbertree[1].computeDecimal() + numbertree[2].computeDecimal());
}

/**
 * Tool for computing the vertical 2nd differences in a 2-arguments table
 * @type {InputTool}
 */
Diff22Arg = new InputTool(Diff22Arg_AreaGenerator, Diff2_builtin);
/**
 * Tool for computing the 2nd differences in a 1-argument table
 * @type {InputTool}
 */
Diff21Arg = new InputTool(Diff21Arg_AreaGenerator, Diff2_builtin);
/**
 * Tool for computing the horizontal 2nd differences in a 2-arguments table
 * @type {InputTool}
 */
Diff22ArgHorizontal = new InputTool(Diff22Arg_Horizontal_AreaGenerator, Diff2_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s for the classic computation of the whole vertical 1st differences
 * in the case of a 1-arguments table
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff1_classic_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var res = [];
    for(var arg0=1; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        res.push( new SuperArea ([hots[0].fromPath([0,0,arg0]), hots[0].fromPath([0,0,arg0-1])],hots[1].fromPath([0,0,arg0])) );
    }
    return res;
}

/**
 * Tool for computing all the first differences of a 1-argument table
 * @type {InputTool}
 */
Diff1_classic = new InputTool(Diff1_classic_AreaGenerator, Diff1_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s for the reverse computation of the whole vertical 1st differences
 * in the case of a 1-arguments table
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff1_reverse_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps-1; arg0++) {
        res.push( new SuperArea ([hots[0].fromPath([0,0,arg0]), hots[0].fromPath([0,0,arg0+1])],hots[1].fromPath([0,0,arg0])) );
    }
    return res;
}

/**
 * Tool for reverse computing all the first differences of a 1-argument table
 * @type {InputTool}
 */
Diff1_reverse = new InputTool(Diff1_reverse_AreaGenerator, Diff1_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s for the classic computation of the whole vertical 1st differences
 * in the case of a 2-arguments table
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff1_vertical_classic_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var res = [];
    for(var arg0=1; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        for(var arg1=0; arg1<hots[0].spec.args[1].nsteps; arg1++) {
            res.push( new SuperArea ([hots[0].fromPath([0,0,arg0,arg1]), hots[0].fromPath([0,0,arg0-1,arg1])],hots[1].fromPath([0,0,arg0,arg1])) );
        }
    }
    return res;
}

/**
 * Tool for computing  all the vertical 1st differences of a 2-arguments table
 * @type {InputTool}
 */
Diff1_vertical_classic = new InputTool(Diff1_vertical_classic_AreaGenerator, Diff1_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s for the reverse computation of the whole vertical 1st differences
 * in the case of a 2-arguments table
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff1_vertical_reverse_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps-1; arg0++) {
        for(var arg1=0; arg1<hots[0].spec.args[1].nsteps; arg1++) {
            res.push( new SuperArea ([hots[0].fromPath([0,0,arg0,arg1]), hots[0].fromPath([0,0,arg0+1,arg1])],hots[1].fromPath([0,0,arg0,arg1])) );
        }
    }
    return res;
}

/**
 * Tool for reverse computing all the vertical 1st differences of a 2-arguments table
 * @type {InputTool}
 */
Diff1_vertical_reverse = new InputTool(Diff1_vertical_reverse_AreaGenerator, Diff1_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s for the classic computation of the whole horizontal 1st differences
 * in the case of a 2-arguments table
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff1_horizontal_classic_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        for(var arg1=1; arg1<hots[0].spec.args[1].nsteps; arg1++) {
            res.push( new SuperArea ([hots[0].fromPath([0,0,arg0,arg1]), hots[0].fromPath([0,0,arg0,arg1-1])],hots[1].fromPath([0,0,arg0,arg1])) );
        }
    }
    return res;
}

/**
 * Tool for computing all the horizontal 1st differences of a 2-arguments table
 * @type {InputTool}
 */
Diff1_horizontal_classic = new InputTool(Diff1_horizontal_classic_AreaGenerator, Diff1_builtin);

/**
 * Generate the source and target \ :js:class:`SuperCell`\ s for the reverse computation of the whole horizontal 1st differences
 * in the case of a 2-arguments table
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply \ :js:func:`Diff1_builtin`\ s
 */
function Diff1_horizontal_reverse_AreaGenerator(selection, hots) {
    if(hots.length < 2)
        return [];
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        for(var arg1=0; arg1<hots[0].spec.args[1].nsteps-1; arg1++) {
            res.push( new SuperArea ([hots[0].fromPath([0,0,arg0,arg1]), hots[0].fromPath([0,0,arg0,arg1+1])],hots[1].fromPath([0,0,arg0,arg1])) );
        }
    }
    return res;
}

/**
 * Tool for reverse computing all the horizontal 1st differences of a 2-arguments table
 * @type {InputTool}
 */
Diff1_horizontal_reverse = new InputTool(Diff1_horizontal_reverse_AreaGenerator, Diff1_builtin);

/**
 * Generate the target entry \ :js:class:`SuperCell`\ s for filling them with a model.
 * This is for 1-arguments tables
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply the model
 */
function Fill_1Arg(selection, hots) {
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        res.push( new SuperArea([hots[0].fromPath([1,0,arg0])],hots[0].fromPath([0,0,arg0])) );
    }
    return res;
}

/**
 * Generate the target entry \ :js:class:`SuperCell`\ s for filling them with a model.
 * This is for 2-arguments tables
 * @param {Array}  selection  This parameter is present for respecting the signature of an AreaGenerator but is unused here
 * @param {Array}  hots       List of \ :js:class:`RootZone`\ s. The differences are computed from the first, and are stored in the second
 * @return {Array}  List of \ :js:class:`SuperArea`\ s on which to apply the model
 */
function Fill_2Arg(selection, hots) {
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        for(var arg1=0; arg1<hots[0].spec.args[1].nsteps; arg1++) {
            // res.push( new SuperArea ([hots[0].fromPath([1,0,arg0,arg1]), hots[0].fromPath([1,1,arg0,arg1+1])],hots[0].fromPath([0,0,arg0,arg1])) );
            res.push( new SuperArea ([hots[0].zones[1].zones[arg0], hots[0].zones[2].zones[arg1]],hots[0].fromPath([0,0,arg0,arg1])) );
        }
    }
    console.log(res);
    return res;
}

function Zero_model_builtin_1Arg(numbertree) {
    return new SmartNumber(0.0);
}

function Zero_model_builtin_2Arg(numbertree) {
    return new SmartNumber(0.0);
}

Fill_zero_1Arg = new InputTool(Fill_1Arg, Zero_model_builtin_1Arg);
Fill_zero_2Arg = new InputTool(Fill_2Arg, Zero_model_builtin_2Arg);

function Get_2Arg_Complete(selection, hots) {
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        for(var arg1=0; arg1<hots[0].spec.args[1].nsteps; arg1++) {
            if(hots[0].fromPath([0,0,arg0,arg1]).isComplete() && hots[0].fromPath([1,0,arg0,arg1]).isComplete() && hots[0].fromPath([1,1,arg0,arg1]).isComplete()) {
                res.push([[hots[0].fromPath([1,0,arg0,arg1]).getSmartNumber().computeDecimal(), hots[0].fromPath([1,1,arg0,arg1]).getSmartNumber().computeDecimal()], hots[0].fromPath([0,0,arg0,arg1]).getSmartNumber().computeDecimal()]);
            }
        }
    }
    return res;
}

function Get_1Arg_Complete(hots) {
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        if(hots[0].fromPath([0,0,arg0]).isComplete() && hots[0].fromPath([1,0,arg0]).isComplete()) {
            res.push([[hots[0].fromPath([1,0,arg0]).getSmartNumber().computeDecimal()], hots[0].fromPath([0,0,arg0]).getSmartNumber().computeDecimal()]);
        }
    }
    return res;
}

function Get_2Arg_CompleteAndValidated(hots) {
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        for(var arg1=0; arg1<hots[0].spec.args[1].nsteps; arg1++) {
            if(hots[0].fromPath([0,0,arg0,arg1]).isCompleteAndValidated() && hots[0].fromPath([1,0,arg0,arg1]).isCompleteAndValidated() && hots[0].fromPath([1,1,arg0,arg1]).isComplete()) {
                res.push([[hots[0].fromPath([1,0,arg0,arg1]).getSmartNumber().computeDecimal(), hots[0].fromPath([1,1,arg0,arg1]).getSmartNumber().computeDecimal()], hots[0].fromPath([0,0,arg0,arg1]).getSmartNumber().computeDecimal()]);
            }
        }
    }
    return res;
}

function Get_1Arg_CompleteAndValidated(hots) {
    var res = [];
    for(var arg0=0; arg0<hots[0].spec.args[0].nsteps; arg0++) {
        if(hots[0].fromPath([0,0,arg0]).isCompleteAndValidated() && hots[0].fromPath([1,0,arg0]).isCompleteAndValidated()) {
            res.push([[hots[0].fromPath([1,0,arg0]).getSmartNumber().computeDecimal()], hots[0].fromPath([0,0,arg0]).getSmartNumber().computeDecimal()]);
        }
    }
    return res;
}
