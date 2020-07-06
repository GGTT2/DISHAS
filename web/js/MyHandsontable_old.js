
/**
 * Specification of a 2D table
 * This information allow to create the correct handsonTable
 */

entry_separator = 1;

var example2D = {
    args : [
        {
            name : 'arg1',
            nsteps : 10,
            ncells : 5,
            type : 'sexagesimal'
        },
        {
            name : 'arg2',
            nsteps : 20,
            ncells : 4,
            type : 'hsitorical'
        }
    ],
    entries : [
        {
            name : 'entry',
            ncells : 4,
            type : 'decimal'
        }
    ],
    padleft : true
};

function dimFromSpec(spec) {
    var nargs = spec.args.length;
    if(nargs === 1) {
        var nl = spec.args[0].nsteps;
        var nc = spec.args[0].ncells;
        for(var i=0;i<spec.entries.length;i++) {
            nc += spec.entries[i].ncells;
            nc += entry_separator; //entry separator
        }
    }
    return [nl,nc];
}

//returns [arg/entry , nth(a/e) , decimal_pos , landmark , [arg1,arg2,...] ]
function getCellType(spec, row, col) {
    var nargs = spec.args.length;
    if(nargs === 1) {
        if(col < spec.args[0].ncells) {
            if(col === spec.args[0].ncells-1)
                return [true,0,col,3,[row]];
            else if(col === spec.args[0].decpos-1)
                return [true,0,col,2,[row]];
            else if(col === 0)
                return [true,0,0,0,[row]];
            else
                return [true,0,col,0,[row]];
        }
        var offset = spec.args[0].ncells;
        for(var i=0;i<spec.entries.length;i++) {
            if(col < offset + spec.entries[i].ncells) {
                if(col === offset + spec.entries[i].ncells-1)
                    return [false,i,col - offset,3,[row]];
                else if(col === offset + spec.entries[i].decpos-1)
                    return [false,i,col - offset,2,[row]];
                else if(col === offset)
                    return [false,i,0,0,[row]];
                else
                    return [false,i,col - offset,0,[row]];
            }
            offset += spec.entries[i].ncells;
            offset += entry_separator;
            if(col < offset && i === 0)
                return [false,i,col-offset,-2,[row]];
            else if(col < offset)
                return [false,i,col-offset,-1,[row]];
        }
    }
}

//expects [ arg/entry , nth(a/e), pos, type, [arg1, arg2, ...] ]
function getCellXY(spec, celltype) {
    var nargs = spec.args.length;
    if(nargs === 1) {
        if(celltype[0]) {
            return [celltype[4][0],celltype[2]];
        }
        else {
            var offset = spec.args[0].ncells;
            for(var i=0;i<celltype[1];i++) {
                offset += spec.entries[i].ncells;
                offset += entry_separator;
            }
            return [celltype[4][0],offset+celltype[2]];
        }
    }
}

/**
 * MetaCell
 * 
 * Define a logical group of cell (cells that pertains to the same format number)
 * 
 * new MetaCell(type, index, arg_pos, pos, spec)
 * @param {boolean} type
 * @param {integer} index
 * @param {Array} arg_pos
 * @param {integer} pos
 * @param {Object} spec
 * @returns {MetaCell}
 * 
 * or
 * 
 * new MetaCell(row, col, spec)
 * @param {integer} row
 * @param {integer} col
 * @param {Object} spec
 * @returns {MetaCell}
 */

function MetaCell(arg1, arg2, arg3, arg4, arg5) {
    if(arg4 === undefined && arg5 === undefined) {
        this.type = undefined;
        this.index = undefined;
        this.arg_pos = undefined;
        this.pos = undefined;
        this.spec = arg3;
        this.row = arg1;
        this.col = arg2;
        
        var cellinfo = getCellType(this.spec, this.row, this.col);
        this.type = cellinfo[0];
        this.index = cellinfo[1];
        this.arg_pos = cellinfo[4];
        this.pos = cellinfo[2];
    }
    else {
        this.type = arg1;
        this.index = arg2;
        this.arg_pos = arg3;
        this.pos = arg4;
        this.spec = arg5;
        this.row = undefined;
        this.col = undefined;
        
        var coordinates = getCellXY(this.spec, [this.type,this.index,this.pos,0,this.arg_pos]);
        this.row = coordinates[0];
        this.col = coordinates[1];
    }
    
    this.subspec = undefined;
    this.cells = undefined;
    this.nargs = this.spec.args.length;
    
    this.isEntry = function() {
        return !this.type;
    };
    this.isArgument = function() {
        return this.type;
    };
    
    if(this.isArgument())
        this.subspec = this.spec.args[this.index];
    else
        this.subspec = this.spec.entries[this.index];
    
    this.cells = [];
    if(this.nargs === 1) {
        if(this.isArgument())
            for(var i=0;i<this.subspec.ncells;i++)
                this.cells.push([this.arg_pos[0],i]);
        else {
            var offset = this.spec.args[0].ncells;
            for(var i=0;i<this.index;i++) {
                offset += this.spec.entries[i].ncells;
                offset += entry_separator;
            }
            for(var i=0;i<this.subspec.ncells;i++)
                this.cells.push([this.arg_pos[0],offset+i]);
        }
    }
    
    this.landmark = getCellType(this.spec, this.row, this.col)[3];
    this.metacells = undefined;
    
    this.isfirst = function() {
        if(this.landmark === 1)
            return true;
        if(this.landmark === 0)
            return false;
        if(this.subspec.decpos === 1 && this.landmark === 2)
            return true;
        if(this.subspec.ncells === 1 && this.landmark === 3)
            return true;
        return false;
    };
    this.isdec = function() {
        if(this.landmark === 2)
            return true;
        if(this.landmark === 0)
            return false;
        if(this.subspec.decpos === 1 && this.landmark === 1)
            return true;
        if(this.subspec.ncells === 1 && this.landmark === 3)
            return true;
        return false;
    };
}

MetaCell.grid = undefined;
MetaCell.spec = undefined;

MetaCell.initGrid = function(spec) {
    MetaCell.grid = [];
    var dim = dimFromSpec(spec);
    var nl = dim[0];
    var nc = dim[1];
    for(var i=0;i<nl;i++) {
        MetaCell.grid.push([]);
        for(var j=0;j<nc;j++)
            MetaCell.grid[i].push(new MetaCell(i,j,spec));
    }
    for(var i=0;i<nl;i++) {
        for(var j=0;j<nc;j++) {
            var cells = MetaCell.grid[i][j].cells;
            MetaCell.grid[i][j].metacells = [];
            for(var k=0;k<cells.length;k++)
                MetaCell.grid[i][j].metacells.push(MetaCell.grid[cells[k][0]][cells[k][1]]);
        }
    }
    MetaCell.spec = spec;
    MetaCell.nc = nc;
    MetaCell.nl = nl;
};

MetaCell.isFullyCompleted = function(row,col) {
    var cells = MetaCell.grid[row][col].cells;
    for(var i=0; i<cells.length; i++)
        if(data[cells[i][0]][cells[i][1]] === '')
            return false;
    return true;
};

MetaCell.isFullySuggested = function(row,col) {
    var cells = MetaCell.grid[row][col].cells;
    for(var i=0; i<cells.length; i++)
        if(!data_props[cells[i][0]][cells[i][1]].suggested)
            return false;
    return true;
};

MetaCell.isInBound = function(row, col) {
    if(row < 0 || row >= MetaCell.nl || col < 0 || col >= MetaCell.nc)
        return false;
    return true;
};

MetaCell.fullError = function(row, col, error) {
    var cells = MetaCell.grid[row][col].cells;
    for(var i=0; i<cells.length; i++)
        data_props[cells[i][0]][cells[i][1]].error = error;
};

MetaCell.fullSuggest = function(row, col, suggest) {
    var cells = MetaCell.grid[row][col].cells;
    for(var i=0; i<cells.length; i++)
        data_props[cells[i][0]][cells[i][1]].suggested = suggest;
};

MetaCell.getSmartNumberAt = function(row,col) {
    var nleft = [];
    var nright = [];
    var count = 0;
    var cells = MetaCell.grid[row][col].cells;
    for(count=0;count<cells.length;count++) {
        nleft.push(Number(data[cells[count][0]][cells[count][1]]));
        if(MetaCell.grid[cells[count][0]][cells[count][1]].landmark === 2)
            break;
    }
    count ++;
    for(var i=count; i<cells.length;i++)
        nright.push(Number(data[cells[i][0]][cells[i][1]]));
    var name = MetaCell.grid[cells[0][0]][cells[0][1]].subspec.type;
    var sign = 1;
    var firstcell = data[cells[0][0]][cells[0][1]];
    if(typeof firstcell === "string" && firstcell.charAt(0) === '-' || typeof firstcell === "number" && firstcell < 0) {
        sign = -1;
        nleft[0] = Math.abs(Number(nleft[0]));
    }
    return new SmartNumber(nleft, nright, 0.0, name, sign);
};

MetaCell.setSmartNumberAt = function(row,col,value,suggested) {
    if(suggested === undefined)
        suggested = true;
    var name = MetaCell.grid[row][col].subspec.type;
    var ndec = MetaCell.grid[row][col].subspec.decpos;
    var nsig = MetaCell.grid[row][col].subspec.ncells - MetaCell.grid[row][col].subspec.decpos;
    
    if(name !== 'none') {
        var num = value.toBase(name,nsig+1);
        num.round(nsig);
        var leftl = num.computeBase(name,nsig).nleft.length;
        if(leftl > ndec) {
            //console.log("ERROR : FIX IT IM TOO LONG!");
        }
        var cells = MetaCell.grid[row][col].cells;
        for(var i=0; i<ndec; i++) {
            if(num.computeBase(name,nsig).nleft[leftl-ndec+i] === undefined)
                data[cells[i][0]][cells[i][1]] = 0;
            else
                data[cells[i][0]][cells[i][1]] = num.computeBase(name,nsig).nleft[leftl-ndec+i];
        }
        for(var i=0;i<nsig; i++) {
            data[cells[i+ndec][0]][cells[i+ndec][1]] = num.computeBase(name,nsig).nright[i];
        }
        if(num.computeBase(name,nsig).sign < 0) {
            data[cells[0][0]][cells[0][1]] = "-"+data[cells[0][0]][cells[0][1]];
        }
    }
    else {
        var cells = MetaCell.grid[row][col].cells;
        data[cells[0][0]][cells[0][1]] = value.computeDecimal().toExponential(5);
    }
    MetaCell.fullError(row,col,false);
    MetaCell.fullSuggest(row,col,suggested);
};

MetaCell.fullyEraseAt = function(row, col) {
    var cells = MetaCell.grid[row][col].cells;
    var ndec = MetaCell.grid[row][col].subspec.decpos;
    for(var i=0; i<ndec; i++) {
        data[cells[i][0]][cells[i][1]] = "";
        data_props[cells[i][0]][cells[i][1]].suggested = true;
    }
};

MetaCell.getNUps = function(row, col, nb) {
    if(nb === undefined)
        var nb = 1;
    var cells = [];
    var counter = 0;
    for(var l=row-1; l>=0; l--) {
        if(counter >= nb)
            break;
        if(MetaCell.isFullyCompleted(l, col)) {
            cells.push([l,col]);
            counter ++;
        }
    }
    return cells;
};

MetaCell.getNDowns = function(line, col, nb) {
    if(nb === undefined)
        var nb = 1;
    var cells = [];
    var counter = 0;
    for(var l=line+1; l<MetaCell.spec.args[0].nsteps; l++) {
        if(counter >= nb)
            break;
        if(isFullyCompleted(l, col)) {
            cells.push([l,col]);
            counter ++;
        }
    }
};

/** ==========================================================================
 * End of metaCell block
 *  ==========================================================================
 */ 

function posInBase(base, index, decindex) {
    if(decindex === undefined)
        var decindex = base[0].length;
    if(index < decindex) {
        var res = index + base[0].length - decindex;
        return [0,res];
    }
    return [1,index-decindex];
}

function getSmartNumbersAtPatch(patch) {
    if(typeof patch[0] === "number") { //patch is couple (row, col)
        return MetaCell.getSmartNumberAt(patch[0], patch[1]).value();
    }
    var res = [];
    for(var i=0;i<patch.length;i++) {
        res.push(getSmartNumbersAtPatch(patch[i]));
    }
    return res;
}

function setSmartNumbersAtPatch(patch, smarts, suggested) {
    if(suggested === undefined)
        suggested = true;
    if(typeof patch[0] === "number") { //patch is couple (row, col)
        MetaCell.setSmartNumberAt(patch[0], patch[1], smarts, suggested);
    }
    for(var i=0;i<patch.length;i++) {
        setSmartNumbersAtPatch(patch[i], smarts[i], suggested);
    }
}

function eraseAtPatch(patch) {
    if(typeof patch[0] === "number") { //patch is couple (row, col)
        MetaCell.fullyEraseAt(patch[0], patch[1]);
    }
    for(var i=0;i<patch.length;i++) {
        eraseAtPatch(patch[i]);
    }
}

function checkPatchMono(patch, option) {
    if(typeof patch[0] === "number") { //patch is couple (row, col)
        return option(patch[0], patch[1]);
    }
    for(var i=0;i<patch.length;i++) {
        if(!checkPatchMono(patch[i], option))
            return false;
    }
    return true;
}

function checkPatch(patch, options) {
    if(options === undefined)
        return true;
    for(var i=0; i<options.length; i++) {
        if(!checkPatchMono(patch, options[i]))
            return false;
    }
    return true;
}

/** ==========================================================================
 * Patch definitions
 *  ==========================================================================
 */ 

//line & col of last input value
function getFirstDifferenceArgs(spec, line, col) {
    var res = [];
    if(line === 0 || !MetaCell.isFullyCompleted(line-1, col)) {
        return [];
    }
    else {
        res = [
            [line-1, col],
            [line, col]
        ];
        var target = new MetaCell(false, 1, [line], 0, spec);
        return [res, [target.row, target.col]];
    }
}

function getFirstDifferenceProp(spec, line, col) {
    var res = [];
    if(MetaCell.isFullyCompleted(line+1, col)) {
        res.push([line+1, col]);
    }
    return res;
}

//line & col of last input value
function getSecondDifferenceArgs(spec, line, col) {
    var res = [];
    if(line === 0 || line === 1 || !MetaCell.isFullyCompleted(line-1, col) || !MetaCell.isFullyCompleted(line-2, col)) {
        return [];
    }
    else {
        res = [
            [line-2, col],
            [line-1, col],
            [line, col]
        ];
        var target = new MetaCell(false, 2, [line], 0, spec);
        return [res, [target.row, target.col]];
    }
}

function getSecondDifferenceProp(spec, line, col) {
    var res = [];
    if(MetaCell.isFullyCompleted(line+1, col)) {
        res.push([line+1, col]);
    }
    if(MetaCell.isFullyCompleted(line+2, col)) {
        res.push([line+2, col]);
    }
    return res;
}

function getForwardInterpolationArgs(spec, line, col, nb) {
    if(nb === undefined)
        var nb = 1;
    var ups = MetaCell.getNUps(line, col, nb);
    if(ups.length < nb)
        return [];
    var entries = [[line, col]].concat(ups);
    var argups = JSON.parse(JSON.stringify(entries));
    for(var i=0;i<argups.length;i++)
        argups[i][1] = 0;
    
    var res = [];
    res.push([]);
    for(var i=0; i<nb+1; i++) {
        if(!MetaCell.isFullyCompleted(argups[i][0], argups[i][1]))
            return [];
        res[0].push([argups[i][0],argups[i][1]]);
    }
    res.push([]);
    for(var i=0; i<nb+1; i++) {
        res[1].push([entries[i][0],entries[i][1]]);
    }
    res.push([]);
    if(!MetaCell.isFullyCompleted(line+1,0))
        return [];
    //obtenir la ligne du bas autrement
    res[2].push([line+1,0]);
    
    if(!MetaCell.isFullySuggested(line+1, col))
        return [];
    return [res, [line+1, col]];
}

/** ==========================================================================
 * Math tool definitions
 *  ==========================================================================
 */ 

hot = undefined;

function customIdentityInterpolation(params) {
    var res = undefined;
    $.ajaxSetup({async: false});
    $.getJSON(Routing.generate('tamas_astro_callAutoToolPython', {'scriptName':'DISHAS_apenon_identity-interpolate_2018-01-18.py', 'option':JSON.stringify(params)}), function(result) {
        res = JSON.parse(result);
    });
    $.ajaxSetup({async: true});
    if(res.hasOwnProperty('error')) {
        var sn = new SmartNumber(0.0);
        alert(res.error);
    }
    else
        var sn = new SmartNumber(res);
    return sn;
}

function builtinLinearInterpolation(params) {
    var arg1 = new SmartNumber(params[0][0]);
    var arg2 = new SmartNumber(params[0][1]);
    var val1 = new SmartNumber(params[1][0]);
    var val2 = new SmartNumber(params[1][1]);
    var newarg = new SmartNumber(params[2][0]);
    
    var slope = (val1.computeDecimal() - val2.computeDecimal())/(arg1.computeDecimal() - arg2.computeDecimal());
    
    var res = val1.computeDecimal() + (newarg.computeDecimal() - arg1.computeDecimal()) * slope;
    
    return new SmartNumber(res);
}

function builtinFirstDifference(params) {
    var val1 = new SmartNumber(params[0]);
    var val2 = new SmartNumber(params[1]);
    
    return new SmartNumber(val2.computeDecimal() - val1.computeDecimal());
}

function builtinSecondDifference(params) {
    var val1 = new SmartNumber(params[0]);
    var val2 = new SmartNumber(params[1]);
    var val3 = new SmartNumber(params[2]);
    
    return new SmartNumber(val3.computeDecimal() - 2*val2.computeDecimal() + val1.computeDecimal());
}

function fillArgsFromTwoFirst1D(spec) {
    if(!MetaCell.isFullyCompleted(0, 0))
        return ;
    if(!MetaCell.isFullyCompleted(1, 0))
        return ;
    
    var val0 = MetaCell.getSmartNumberAt(0,0);
    var val1 = MetaCell.getSmartNumberAt(1,0);
    
    var init = val0.computeDecimal();
    var step = val1.computeDecimal() - val0.computeDecimal();
    
    for(var i=2; i<spec.args[0].nsteps; i++) {
        MetaCell.setSmartNumberAt(i,0,new SmartNumber(init + i*step));
    }
    hot.render();
}


/** ==========================================================================
 * End of Mathematic tool block
 *  ==========================================================================
 */ 


function createHandsonTable(spec, containerId, toolBox) {
    
    var container = document.getElementById(containerId);
    
    if(toolBox === undefined)
        toolBox = {};
    var buttonGenerate2 = toolBox["arg1D_2"];
    var buttonValidateAll = toolBox["validate_all"];
    if(buttonGenerate2 !== undefined)
    $('#'+buttonGenerate2).click(function(e){
        fillArgsFromTwoFirst1D(spec);
    });
    if(buttonValidateAll !== undefined)
        $('#'+buttonValidateAll).click(function(e){
            for(var i=0;i<nl;i++) {
                for(var j=0;j<nc;j++) {
                    if(data[i][j] !== "")
                        data_props[i][j].suggested = false;
                }
            }
            hot.render();
        });
    
    var nargs = spec.args.length;
    
    var auto_tools = {
        first_order_diff: [getFirstDifferenceArgs, getFirstDifferenceProp, builtinFirstDifference],
        second_order_diff: [getSecondDifferenceArgs, getSecondDifferenceProp, builtinSecondDifference],
        forward_interpolation: [getForwardInterpolationArgs, undefined, builtinLinearInterpolation, {suggested:true}]
    };
    
    var dims = dimFromSpec(spec);
    var nl = dims[0];
    var nc = dims[1];
    
    data = [];
    data_props = [];
    var selection = [0,0,0,0];
    var selected = 0;
    for(var i=0;i<nl;i++) {
        data.push([]);
        data_props.push([]);
        for(var j=0;j<nc;j++) {
            data[i].push("");
            data_props[i].push({
                suggested : true,
                error : false
            });
        }
    }
    
    MetaCell.initGrid(spec);
    
    if(nargs === 1) {
        var postRender = function(test) {
            if( typeof hot == 'undefined') {
                return;
            }
            var roffset = hot.rowOffset();
            var coffset = hot.colOffset();
            $(".ht_master tr").each(function(l) {
                    $( "td", this ).each(function(c) {
                        var line = roffset + l;
                        var col = coffset + c;
                        
                        var metacell = MetaCell.grid[line][col];
                        
                        if(metacell.isdec())
                            $( this ).addClass('green-right');
                        if(metacell.isfirst())
                            $( this ).addClass('black-left-thick');
                        if(metacell.landmark < 0) {
                            $( this ).addClass('no-border');
                            $( this ).addClass('black-left-thick');
                        }
                        if(metacell.type) {
                            $( this ).addClass('arg');
                        }
                        
                        $( this ).addClass('red-bottom');
                        if(data_props[line][col].error)
                            $( this ).css("color","red");
                        if(data_props[line][col].suggested)
                            $( this ).css("color","red");
                    });
                });
        };
        
        var dataChanged = function(edit) {
            if(!edit)
                return;
            for(nedit=0;nedit<edit.length;nedit++) {
                var row = edit[nedit][0];
                var col = edit[nedit][1];
                var val = edit[nedit][3];
                
                var celltype = getCellType(spec, row, col);
                var metacell = MetaCell.grid[row][col];
                var cells = metacell.cells;
                
                if(val === "") {
                    data_props[row][col].suggested = true;
                    //erase corresponding auto_tools
                    for (var key in auto_tools) {
                        if (auto_tools.hasOwnProperty(key)) {
                            var options = {};
                            var suggested = false;
                            if(auto_tools[key][3] !== undefined)
                                options = auto_tools[key][3];
                            if(options.hasOwnProperty("suggested"))
                                suggested = options.suggested;

                            if(auto_tools[key][1] === undefined)
                                continue;
                            var prop_patch = auto_tools[key][1](spec, row, col);
                            for(var i=0; i<prop_patch.length; i++) {
                                if(!MetaCell.isFullyCompleted(prop_patch[i][0], prop_patch[i][1]))
                                    continue;
                                var ppatch = auto_tools[key][0](spec, prop_patch[i][0], prop_patch[i][1]);
                                if(ppatch.length === 2) {
                                    eraseAtPatch(ppatch[1]);
                                }
                            }

                        }
                    }
                }
                else {
                    for(var i=0; i<cells.length; i++) {
                        data_props[cells[i][0]][cells[i][1]].suggested = false;
                    }
                    
                    var index_in_base = posInBase(name_to_base[metacell.subspec.type], metacell.pos, metacell.subspec.decpos);
                    if(index_in_base[1] < 0)
                        index_in_base[1] = 0;
                    if(index_in_base[0] === 1 && index_in_base[1] >= name_to_base[metacell.subspec.type][1].length)
                        index_in_base[1] = name_to_base[metacell.subspec.type][1].length - 1;

                    //check du flag error
                    if(!(index_in_base[0] === 0 && metacell.pos === 0) 
                            && val >= name_to_base[metacell.subspec.type][index_in_base[0]][index_in_base[1]])
                        data_props[row][col].error = true;
                    else
                        data_props[row][col].error = false;

                    if(!metacell.type && metacell.index === 0) { // we fill in an entry
                        
                        if(MetaCell.isFullyCompleted(row, col)) {
                            for (var key in auto_tools) {
                                if (auto_tools.hasOwnProperty(key)) {
                                    var options = {};
                                    var suggested = false;
                                    if(auto_tools[key][3] !== undefined)
                                        options = auto_tools[key][3];
                                    if(options.hasOwnProperty("suggested"))
                                        suggested = options.suggested;
                                    var patch = auto_tools[key][0](spec, row, col);
                                    if(patch.length === 2) {
                                        var params = getSmartNumbersAtPatch(patch[0]);
                                        var res = auto_tools[key][2](params);
                                        setSmartNumbersAtPatch(patch[1], res, suggested);
                                    }
                                        
                                    if(auto_tools[key][1] === undefined)
                                        continue;
                                    var prop_patch = auto_tools[key][1](spec, row, col);
                                    for(var i=0; i<prop_patch.length; i++) {
                                        if(!MetaCell.isFullyCompleted(prop_patch[i][0], prop_patch[i][1]))
                                            continue;
                                        var ppatch = auto_tools[key][0](spec, prop_patch[i][0], prop_patch[i][1]);
                                        if(ppatch.length === 2) {
                                            var params = getSmartNumbersAtPatch(ppatch[0]);
                                            var res = auto_tools[key][2](params);
                                            setSmartNumbersAtPatch(ppatch[1], res, suggested);
                                        }
                                    }
                                    
                                }
                            }
                        }
                        
                    }
                }
            }
            hot.render();
        };
        
        var dataSelected = function(line1, col1, line2, col2, info) {
            if(line1 === line2 && col1 === col2) {
                selected = 1;
                selection = [line1, col1];
                return ;
            }
            if(col1 > col2) {
                var coll = col2;
                var colr = col1;
                var linel = line2;
                var liner = line1;
            }
            else {
                var coll = col1;
                var colr = col2;
                var linel = line1;
                var liner = line2;
            }

            var type1 = getCellType(spec, linel, coll);
            var type2 = getCellType(spec, liner, colr);
                      
            if(type1[3] === 0 && type2[3] === 3) {
                selected = 2;
                selection = [linel, coll, liner, colr];
            }
            else {
                selected = 0;
            }
        };
        
        var beforeKey = function(key) {
            if(key.key === "o" || key.key === "p" || key.key === "m") {
                var delta = 0;
                if(key.key === "o")
                    delta = -1;
                if(key.key === "p")
                    delta = 1;
                if(key.key === "m")
                    delta = 0;
                if(selected === 1 && data[selection[0]][selection[1]]!== "") {
                    var sn = MetaCell.getSmartNumberAt(selection[0],selection[1]);
                    var pos = MetaCell.grid[selection[0]][selection[1]].pos;
                    if(sn.isZero())
                        sn.addOne(pos,delta);
                    else
                        sn.addOne(pos,delta);
                    MetaCell.setSmartNumberAt(selection[0],selection[1],sn);
                    hot.setDataAtCell(selection[0],selection[1],data[selection[0]][selection[1]]);
                }
                key.stopImmediatePropagation();
            }
        };
    }
    
    if(nargs === 1) {
        var columns = [];
        for(var i=0;i<nc;i++) {
            var metacell = MetaCell.grid[0][i];
            var colprop = {};
            if(metacell.landmark < 0) {
                colprop.readOnly = true;
                colprop.width = 100;
                if(metacell.landmark === -2)
                    colprop.width = 300;
            }
            if(!metacell.type && metacell.index > 0) {
                colprop.readOnly = true;
            }
            if(metacell.isfirst()) {
                colprop.width = 100;
            }
            columns.push(colprop);
        }
    }
    hot = new Handsontable(container, {
        rowHeaders: false,
        colHeaders : false,
        columns : columns,
        //contextMenu: true,
        afterRender : postRender,
        afterChange : dataChanged,
        afterSelection : dataSelected,
        beforeKeyDown : beforeKey,
        readOnly : false,
        data: data
    });
    
    hot.render();
    
    /*writeDecimalTo(spec, 10, 2, 114.5);
    hot.render();*/
}