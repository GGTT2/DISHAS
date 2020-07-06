from zone import Zone, Cartesian
from metazone import MetaZone, SuperCell
import copy
from typing import Optional, List, Tuple, Dict, Any


spec_header: Dict[str, Any] = {
    "name": "header",
    "ncells": 1,
    "nsteps": 1,
    "decpos": 1,
    "type": "decimal"
}


class Table1ArgZone_Arg(Zone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.info = {
            'type': 1,
            'index': 0
        }
        self.zones: List[Tuple[SuperCell, Cartesian]]  # type: ignore
        for i in range(self.spec['args'][0]['nsteps']):
            positions = []
            for k in range(self.spec['args'][0]['ncells']):
                positions.append((1, k))
            self.add_zone(SuperCell(spec['args'][0], positions, info=self.info), (i, 0))

    def next_arg(self, arg: int, delta: int, index: int) -> SuperCell:
        return self.zones[index + delta][0]


class Table1ArgZone_Entry(Zone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(spec)
        self.spec = spec
        self.info = {
            'type': 0,
            'index': 0
        }
        self.zones: List[Tuple[SuperCell, Cartesian]]  # type: ignore
        for i in range(self.spec['args'][0]['nsteps']):
            positions = []
            for k in range(self.spec['entries'][0]['ncells']):
                positions.append((1, k))
            self.add_zone(SuperCell(spec['entries'][0], positions, info=self.info), (i, 0))

    def next_arg(self, arg: int, delta: int, index: int) -> SuperCell:
        return self.zones[index + delta][0]


class Table1ArgZone_Main(MetaZone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        delta = 0
        if 'headers' not in self.info:
            self.info['headers'] = False
        if self.info['headers']:
            delta = 1
        self.add_zone(Table1ArgZone_Entry(spec, info=self.info), (delta, spec['args'][0]['ncells']))
        self.add_zone(Table1ArgZone_Arg(spec, info=self.info), (delta, 0))
        if self.info['headers']:
            self.add_zone(Table1ArgZone_HeaderTop(info={'length': self.C, 'name': self.spec['args'][0]['name'], 'entry_pos': self.spec['args'][0]['ncells']}), (0, 0))
        self.setup()

    def from_path(self, path: List[int]) -> Zone:
        if len(path) == 0:
            return self
        if path[0] == 0:
            return self.zones[0][0].from_path(path[2:])
        if path[0] == 1:
            return self.zones[1][0].from_path(path[2:])
        raise ValueError("Incorrect path coordinates")


class Table1ArgZone_HeaderTop(Zone):
    def __init__(self, info: dict):
        super().__init__(info=info)
        self.info = {
            'type': 2,
            'index': 0,
            'zone': 2
        }
        self.zones: List[Tuple[SuperCell, Cartesian]]  # type: ignore
        for i in range(info['length']):
            self.add_zone(SuperCell(spec_header, [(0, 0)], info=self.info), (0, i))
        self.zones[0][0].zones[0][0].val = info['name']
        self.zones[info['entry_pos']][0].zones[0][0].val = 'entry'

"""

"""


class Table2ArgZone_Arg0(Zone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.info = {
            'type': 1,
            'index': 0,
            'zone': 0
        }
        self.zones: List[Tuple[SuperCell, Cartesian]]  # type: ignore
        for i in range(spec['args'][0]['nsteps']):
            positions = []
            for k in range(spec['args'][0]['ncells']):
                positions.append((0, k))
            self.add_zone(SuperCell(spec['args'][0], positions, info=self.info), (i, 0))

    def next_arg(self, arg: int, delta: int, index: int) -> Zone:
        if arg == 0:
            return self.zones[index + delta][0]
        else:
            return self

    def from_path(self, zone_coordinates: List[int]) -> Zone:
        if len(zone_coordinates) == 0:
            return self
        return self.zones[zone_coordinates[0]][0].from_path(zone_coordinates[2:])


class Table2ArgZone_Arg1(Zone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.info = {
            'type': 1,
            'index': 1,
            'zone': 1
        }
        self.zones: List[Tuple[SuperCell, Cartesian]]  # type: ignore
        for i in range(spec['args'][1]['nsteps']):
            positions = []
            for k in range(spec['args'][1]['ncells']):
                positions.append((0, k))
            self.add_zone(SuperCell(spec['args'][1], positions, info=self.info), (0, i * spec['cwidth']))

    def next_arg(self, arg: int, delta: int, index: int) -> Zone:
        if arg == 1:
            return self.zones[index + delta][0]
        else:
            return self

    def from_path(self, zone_coordinates: List[int]) -> Zone:
        if len(zone_coordinates) == 0:
            return self
        return self.zones[zone_coordinates[1]][0].from_path(zone_coordinates[2:])


class Table2ArgZone_EntryLine(Zone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.indexInParent: int
        self.parent: Zone
        for i in range(spec['args'][1]['nsteps']):
            positions = []
            for k in range(spec['entries'][0]['ncells']):
                positions.append((0, k))
            self.add_zone(SuperCell(spec['entries'][0], positions, info=self.info), (0, i * spec['cwidth']))

    def next_arg(self, arg: int, delta: int, index: int) -> Zone:
        if arg == 1:
            return self.zones[index + delta][0]
        else:
            if self.indexInParent + delta >= len(self.parent.zones):
                raise ValueError("Incorrect coordinates")
            return self.parent.zones[self.indexInParent + delta][0]


class Table2ArgZone_Entry(Zone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.info = {
            'type': 0,
            'index': 0,
            'zone': 2
        }
        for i in range(spec['args'][0]['nsteps']):
            self.add_zone(Table2ArgZone_EntryLine(spec, info=self.info), (i, 0))


class Table2ArgZone_HeaderTop(Zone):
    def __init__(self, info: dict):
        super().__init__(info=info)
        self.info = {
            'type': 2,
            'index': 0,
            'zone': 2
        }
        self.zones: List[Tuple[SuperCell, Cartesian]]  # type: ignore
        spec = copy.deepcopy(spec_header)
        spec['ncells'] = info['cwidth']
        for i in range(info['number']):
            positions = []
            for c in range(spec['ncells']):
                positions.append((0, c))
            self.add_zone(SuperCell(spec_header, positions, info=self.info), (0, i * spec['ncells']))
        self.zones[info['number'] // 2][0].zones[0][0].val = info['name']


class Table2ArgZone_HeaderLeft(Zone):
    def __init__(self, info: dict):
        super().__init__(info=info)
        self.info = {
            'type': 2,
            'index': 0,
            'zone': 2
        }
        self.zones: List[Tuple[SuperCell, Cartesian]]  # type: ignore
        for i in range(info['length']):
            self.add_zone(SuperCell(spec_header, [(0, 0)], info=self.info), (i, 0))
        self.zones[self.R // 2][0].zones[0][0].val = info['name']


class Table2ArgZone_Main(MetaZone):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.spec['cwidth'] = max(spec['args'][1]['ncells'], spec['entries'][0]['ncells'])
        delta = 0
        if 'headers' not in self.info:
            self.info['headers'] = False
        if self.info['headers']:
            delta = 1
        self.add_zone(Table2ArgZone_Arg0(self.spec), (1 + delta, 0 + delta))
        self.add_zone(Table2ArgZone_Arg1(self.spec), (0 + delta, self.spec['args'][0]['ncells'] + delta))
        self.add_zone(Table2ArgZone_Entry(self.spec), (1 + delta, self.spec['args'][0]['ncells'] + delta))
        if self.info['headers']:
            self.add_zone(Table2ArgZone_HeaderTop({'number': self.spec['args'][1]['nsteps'], 'name': self.spec['args'][1]['name'], 'cwidth': self.spec['cwidth']}), (0, self.spec['args'][0]['ncells'] + 1))
            self.add_zone(Table2ArgZone_HeaderLeft({'length': self.R - 1, 'name': self.spec['args'][0]['name']}), (1, 0))
        self.setup()

    def from_path(self, path: List[int]) -> Zone:
        if len(path) == 0:
            return self
        if path[0] == 0:
            return self.zones[2][0].from_path(path[2:])
        if path[0] == 1 and path[1] == 0:
            return self.zones[0][0].from_path(path[2:])
        if path[0] == 1 and path[1] == 1:
            return self.zones[1][0].from_path(path[2:])
        return super().from_path(path)


if __name__ == '__main__':
    arg1_spec = {
        'args': [
            {
                'name': 'a',
                'ncells': 3,
                'nsteps': 90,
                'decpos': 1,
                'type': 'integer_and_sexagesimal'
            },
        ],
        'entries': [
            {
                'name': 'entry',
                'decpos': 1,
                'ncells': 4,
                'type': 'sexagesimal'
            }
        ],
        'cwidth': 4
    }

    main_zone = Table1ArgZone_Main(arg1_spec)
