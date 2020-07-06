/* global Zone */
/* global CartesianCoordinates */
/* global SuperCell */
/* global MetaZone */


/* eslint-disable */
/**
 * Each \ :js:class:`Zone`\  containing \ :js:class:`SuperCell`\ s **MUST** implement such a method. It returns
 * the delta-th neighbour of a \ :js:class:`SuperCell`\  (specified by its index in the list of sub-\ :js:class:`Zone`\ s),
 * along the given argument.
 * @param  {Number} arg    index of the argument
 * @param  {Number} delta  the returned \ :js:class:`SuperCell`\  will be the delta-th neighbour
 * @param  {Number} index  index of the \ :js:class:`SuperCell`\ 
 * @return {SuperCell}     the delta-th neighbour
 */
function nextArg_example(arg, delta, index) {
}

if(false) {
    /**
     * A Layout \ :js:class:`MetaCell`\  takes as a parameter a specification of the table.
     * @example
     * {
     *     args: [
     *         {
     *             decpos: 1,
     *             name: "angle",
     *             ncells: 2,
     *             nsteps: 90,
     *             type: "sexagesimal"
     *         }
     *     ],
     *     entries: [
     *         {
     *             decpos: 1,
     *             name: "Entry",
     *             ncells: 4,
     *             type: "sexagesimal"
     *         }
     *     ]
     * }
     * @type {Object}
     */
    var spec_example = {
        args: [
            {
                decpos: 1,
                name: "angle",
                ncells: 2,
                nsteps: 90,
                type: "sexagesimal"
            }
        ],
        entries: [
            {
                decpos: 1,
                name: "Entry",
                ncells: 4,
                type: "sexagesimal"
            }
        ]
    };
}
/* eslint-enable */

/* ================================================== */
//implementation of the standard layout for 1 arg tables
class Table1ArgZone_Arg extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        this.infos = {
            type: 1,
            index: 0
        };
        for(var i=0; i<spec.args[0].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.args[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.args[0], positions, {"argPos0" : i, "argPos" : [i]}), new CartesianCoordinates(i,0));
        }
    }
    nextArg(arg, delta, index) {
        return this.zones[index + delta];
    }
}

class Table1ArgZone_Entry extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        this.infos = {
            type: 0,
            index: 0
        };
        for(var i=0; i<spec.args[0].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.entries[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.entries[0], positions, {"argPos0" : i, "argPos" : [i]}), new CartesianCoordinates(i,0));
        }
    }
    nextArg(arg, delta, index) {
        return this.zones[index + delta];
    }
}

class Table1ArgZone extends MetaZone {
    /**
     * RootZone for a standard layout 1-argument table
     * @param  {Object}  spec  Dictionary specifying the template of the table
     * @param  {Object}  info  Dictionary of additional custom information
     * @return {Table1ArgZone}
     */
    constructor(spec, info) {
        super(info, true);
        this.spec = spec;
        this.addZone(new Table1ArgZone_Entry(spec), new CartesianCoordinates(1,spec.args[0].ncells));
        this.addZone(new Table1ArgZone_Arg(spec), new CartesianCoordinates(1,0));
        this.setup();
        //this.buildGrid();
    }
    fromPath(path) {
        if(path.length === 0)
            return this;
        if(path[0] === 0)
            return this.zones[0].fromPath(path.slice(2));
        if(path[0] === 1)
            return this.zones[1].fromPath(path.slice(2));
    }
}

/* ================================================== */
//implementation of the standard layout for 2arg tables
class Table2ArgZone_Arg0 extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        this.infos = {
            type: 1,
            index: 0,
            zone: 0
        };
        for(var i=0; i<spec.args[0].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.args[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.args[0], positions, {"argPos0" : i, "argPos" : [i, 0]}), new CartesianCoordinates(i,0));
        }
    }
    nextArg(arg, delta, index) {
        if(arg === 0)
            return this.zones[index + delta];
        else {
            return this;
        }
    }
    fromPath(path) {
        if(path.length === 0)
            return this;
        return this.zones[path[0]].fromPath(path.slice(2));
    }
}

class Table2ArgZone_Arg1 extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        this.infos = {
            type: 1,
            index: 1,
            zone: 1
        };
        for(var i=0; i<spec.args[1].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.args[1].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.args[1], positions, {"argPos1" : i, "argPos" : [0, i]}), new CartesianCoordinates(0,i*spec.cwidth));
        }
    }
    nextArg(arg, delta, index) {
        if(arg === 1)
            return this.zones[index + delta];
        else {
            return this;
        }
    }
    fromPath(path) {
        if(path.length === 0)
            return this;
        return this.zones[path[1]].fromPath(path.slice(2));
    }
}

class Table2ArgZone_Entry extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        this.infos = {
            type: 0,
            index: 0,
            zone: 2
        };
        var that = this;
        for(var i=0; i<spec.args[0].nsteps; i++) {
            this.addZone(new Table2ArgZone_EntryLine(spec, {"argPos0" : i}), new CartesianCoordinates(i,0));
        }
        /*forWrapper(spec.args[0].nsteps, 0, 100,
            function(i) {
                that.addZone(new Table2ArgZone_EntryLine(spec, {argPos0 : i}), new CartesianCoordinates(i,0));
            }, function() {
            }, 5);*/
    }
}

class Table2ArgZone_EntryLine extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        for(var i=0; i<spec.args[1].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.entries[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.entries[0], positions, {"argPos1" : i, "argPos" : [this.infos.argPos0, i]}), new CartesianCoordinates(0,i*spec.cwidth));
        }
    }
    nextArg(arg, delta, index) {
        if(arg === 1)
            return this.zones[index + delta];
        else {
            if(this.parent.zones[this.indexInParent + delta] === undefined)
                return null;
            return this.parent.zones[this.indexInParent + delta].zones[index];
        }
    }
}

class Table2ArgZone extends MetaZone {
    /**
     * RootZone for a standard layout 2-arguments table
     * @param  {Object}  spec  Dictionnary specifying the template of the table
     * @param  {Object}  info  Dictionnary of additionnal custom informations
     * @return {Table2ArgZone}
     */
    constructor(spec, info) {
        super(info, true);
        this.spec = spec;
        this.spec.cwidth = Math.max(spec.args[1].ncells, spec.entries[0].ncells);
        this.addZone(new Table2ArgZone_Entry(this.spec), new CartesianCoordinates(1,this.spec.args[0].ncells));
        this.addZone(new Table2ArgZone_Arg0(this.spec), new CartesianCoordinates(1,0));
        this.addZone(new Table2ArgZone_Arg1(this.spec), new CartesianCoordinates(0,this.spec.args[0].ncells));
        this.setup();
        //this.buildGrid();
    }
    fromPath(path) {
        if(path.length === 0)
            return this;
        if(path[0] === 0)
            return this.zones[0].fromPath(path.slice(2));
        if(path[0] === 1 && path[1] === 0)
            return this.zones[1].fromPath(path.slice(2));
        if(path[0] === 1 && path[1] === 1)
            return this.zones[2].fromPath(path.slice(2));
    }
}

/* =================================== */
// Definition of virtual zones for computing 2-args quantities
// spec will be the same as the table, with enties changed (with ncells = 1; type="decimal")

class ComputationZone2Arg extends MetaZone {
    constructor(spec, info) {
        super(info, true);
        this.spec = spec;

        this.addZone(new Table2ArgZone_Entry(this.spec), new CartesianCoordinates(0,0));
        
        this.setup();
    }
    fromPath(path) {
        if(path.length === 0)
            return this;
        if(path[0] === 0)
            return this.zones[0].fromPath(path.slice(2));
    }
}

class ComputationZone1Arg extends MetaZone {
    constructor(spec, info) {
        super(info, true);
        this.spec = spec;
        
        this.addZone(new Table1ArgZone_Entry(this.spec), new CartesianCoordinates(0,0));
        
        this.setup();
    }
    fromPath(path) {
        if(path.length === 0)
            return this;
        if(path[0] === 0)
            return this.zones[0].fromPath(path.slice(2));
    }
}

/* =================================== */
// Definition of graphical info zones

class VerticalInfoZone extends MetaZone {
    constructor(spec, info) {
        if(info === undefined)
            info = {nargs : 2};
        //info.type = 2;
        info.index = 0;
        super(info, true);

        this.spec = spec;
        spec.entries[0].nsteps = spec.args[0].nsteps;

        var zoneInfo = {};
        for(var key in info) {
            zoneInfo[key] = info[key];
        }
        zoneInfo.type = 2;
        this.addZone(new VerticalInfoZone_Entry(spec.entries[0], zoneInfo), new CartesianCoordinates(0,0));
        if(info.show_args) {
            var positions = [];
            for(var k=0; k<spec.args[1].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.args[1], positions, {"type": 3, "argPos": 0}), new CartesianCoordinates(0,0));
        }

        this.setup();
    }
}

class VerticalInfoZone_Entry extends Zone {
    constructor(spec, info) {
        if(info === undefined)
            info = {nargs : 2};
        info.type = 2;
        info.index = 0;
        super(info, true);
        this.spec = spec;
        
        for(var i=0; i<spec.nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec, positions, {"argPos0" : i}), new CartesianCoordinates(i+((this.infos.nargs === 1)?1:1),0));
        }
    }
}

class HorizontalInfoZone extends MetaZone {
    constructor(spec, info) {
        if(info === undefined)
            info = {};
        //info.type = 2;
        info.index = 0;
        super(info, true);
        this.spec = spec;
        
        var zoneInfo = {};
        for(var key in info) {
            zoneInfo[key] = info[key];
        }
        zoneInfo.type = 2;
        this.addZone(new HorizontalInfoZone_Entry(spec, zoneInfo), new CartesianCoordinates(0,0));
        if(info.show_args) {
            var positions = [];
            for(var k=0; k<spec.args[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.args[0], positions, {"type": 1, "argPos": 0}), new CartesianCoordinates(0,0));
        }
        this.setup();
    }
}

class HorizontalInfoZone_Entry extends Zone {
    constructor(spec, info) {
        if(info === undefined)
            info = {};
        info.index = 0;
        super(info, true);
        this.spec = spec;
        var delta = 1;
        if(info.show_args) {
            delta = 0;
        }
        for(var i=0; i<spec.args[1].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.entries[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.entries[0], positions, {"argPos1" : i}), new CartesianCoordinates(0,i*spec.entries[0].ncells+spec.args[0].ncells + delta));
        }
    }
}


///////////////////////////////////

/* ================================================== */
//implementation of an alternative layout for 1arg tables
class Alternative_Table1ArgZone_Arg extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        this.infos = {
            type: 1,
            index: 0
        };
        for(var i=0; i<spec.args[0].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.args[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.args[0], positions, {argPos0 : i}), new CartesianCoordinates(0,i*spec.args[0].ncells));
        }
    }
    nextArg(arg, delta, index) {
        return this.zones[index + delta];
    }
}

class Alternative_Table1ArgZone_Entry extends Zone {
    constructor(spec, info) {
        super(info);
        this.spec = spec;
        this.infos = {
            type: 0,
            index: 0
        };
        for(var i=0; i<spec.args[0].nsteps; i++) {
            var positions = [];
            for(var k=0; k<spec.entries[0].ncells; k++) {
                positions.push(new CartesianCoordinates(0,k));
            }
            this.addZone(new SuperCell(spec.entries[0], positions, {"argPos0" : i}), new CartesianCoordinates(0,i*spec.args[0].ncells));
        }
    }
    nextArg(arg, delta, index) {
        return this.zones[index + delta];
    }
}

class Alternative_Table1ArgZone extends MetaZone {
    constructor(spec, info) {
        super(info, true);
        this.spec = spec;
        this.addZone(new Alternative_Table1ArgZone_Entry(spec), new CartesianCoordinates(1,0));
        this.addZone(new Alternative_Table1ArgZone_Arg(spec), new CartesianCoordinates(0,0));
        this.setup();
        //this.buildGrid();
    }
    fromPath(path) {
        if(path.length === 0)
            return this;
        if(path[0] === 0)
            return this.zones[0].fromPath(path.slice(2));
        if(path[0] === 1)
            return this.zones[1].fromPath(path.slice(2));
    }
}
