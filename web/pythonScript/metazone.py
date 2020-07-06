
from zone import Zone, MainZone, Cell, Cartesian
from conversion import RadixBase, ndigit_for_radix, NumberView
import copy
import json
import sys
from typing import List, Optional, Union, Any, Tuple, Dict


class MetaZone(MainZone):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.spec: dict

        self.grid: List[List[Optional['MetaCell']]]
        self.super_grid: List[List[Optional['SuperCell']]]
        self.data: List[List[Optional[int]]]
        self.css_grid: List[List[Optional[List[str]]]]

    def setup(self):
        super().setup()
        self.css_grid = []
        for i in range(self.R):
            self.css_grid.append([])
            for j in range(self.C):
                self.css_grid[i].append(self.gridXYCSS(i, j))

    def gridXYCSS(self, line: int, col: int) -> List[str]:
        metacell = self.grid[line][col]
        res = []
        if metacell is None:
            res.append('no-border')
        else:
            if metacell.is_dec():
                res.append('isdec')
            if metacell.is_first():
                res.append('isfirst')
            if metacell.is_last():
                res.append('islast')
            if metacell.is_entry():
                res.append('entry')
            if metacell.is_argument():
                res.append('argument')
            if len(metacell.info['root'].spec['args']) == 1:
                if metacell.zone_coordinates[-2] % 2 == 0:
                    res.append('even0')
                else:
                    res.append('odd0')
            else:
                if (metacell.zone_coordinates[-3]) % 2 == 0:
                    res.append('even0')
                else:
                    res.append('odd0')
            if metacell.is_info():
                res.append('info')
        return res

    def as_decimal_JSON(self) -> dict:
        raise NotImplementedError

    def as_original_JSON(self) -> dict:
        raise NotImplementedError

    def from_original_JSON(self, original_JSON: str, spec: dict = None):
        original = json.loads(original_JSON)
        # original = original_JSON
        for i in range(len(self.spec['args'])):
            original_arg = original['args']['argument'+str(i + 1)]
            for k in range(self.spec['args'][i]['nsteps']):
                if k >= len(original_arg):
                    break
                sc = self.from_path([1, i]).zones[k][0]
                if spec is None:
                    for c in range(min(len(sc.zones), len(original_arg[k]['value']))):
                        sc.zones[c][0].fill(original_arg[k]['value'][c], False)
                else:

                    left = copy.deepcopy(original_arg[k]['value'])
                    full = True
                    for val in left:
                        if val == '':
                            full = False
                            break
                    if not full:
                        continue
                    sign = 1
                    if left[0][0] == '-':
                        left[0] = left[0][1:]
                        sign = -1
                    right = [int(val) for val in left[spec['args'][i]['decpos']:]]
                    left = [int(val) for val in left[:spec['args'][i]['decpos']]]
                    left.reverse()
                    sn = RadixBase.name_to_base[spec['args'][i]['type']].type(left, right, remainder=0.0, sign=sign)
                    sc.set_number_view(sn, False)

                sc.props['user_comment'] = original_arg[k]['comment']
                if not original_arg[k]['suggested']:
                    sc.validate_non_empty()

        for i in range(self.spec['args'][0]['nsteps']):
            if i >= len(original['entry']):
                break
            if len(self.spec['args']) == 1:
                sc = self.from_path([0, 0]).zones[i][0]
                if spec is None:
                    for c in range(min(len(sc.zones), len(original['entry'][i]['value']))):
                        sc.zones[c][0].fill(original['entry'][i]['value'][c], False)
                else:

                    left = copy.deepcopy(original['entry'][i]['value'])
                    full = True
                    for val in left:
                        if val == '':
                            full = False
                            break
                    if not full:
                        continue
                    sign = 1
                    if left[0][0] == '-':
                        left[0] = left[0][1:]
                        sign = -1
                    right = [int(val) for val in left[spec['entries'][0]['decpos']:]]
                    left = [int(val) for val in left[:spec['entries'][0]['decpos']]]
                    left.reverse()
                    sn = RadixBase.name_to_base[spec['entries'][0]['type']].type(left, right, remainder=0.0, sign=sign)
                    sc.set_number_view(sn, False)

                sc.props['user_comment'] = original['entry'][i]['comment']
                if not original['entry'][i]['suggested']:
                    sc.validate_non_empty()
            elif len(self.spec['args']) == 2:
                for j in range(self.spec['args'][1]['nsteps']):
                    if j >= len(original['entry'][i]):
                        break
                    sc = self.from_path([0, 0]).zones[i][0].zones[j][0]
                    if spec is None:
                        for c in range(min(len(sc.zones), len(original['entry'][i][j]['value']))):
                            sc.zones[c][0].fill(original['entry'][i][j]['value'][c], False)
                    else:

                        left = copy.deepcopy(original['entry'][i][j]['value'])
                        full = True
                        for val in left:
                            if val == '':
                                full = False
                                break
                        if not full:
                            continue
                        sign = 1
                        if left[0][0] == '-':
                            left[0] = left[0][1:]
                            sign = -1
                        right = [int(val) for val in left[spec['entries'][0]['decpos']:]]
                        left = [int(val) for val in left[:spec['entries'][0]['decpos']]]
                        left.reverse()
                        sn = RadixBase.name_to_base[spec['entries'][0]['type']].type(left, right, remainder=0.0,
                                                                                     sign=sign)
                        sc.set_number_view(sn, False)

                    sc.props['user_comment'] = original['entry'][i][j]['comment']
                    if not original['entry'][i][j]['suggested']:
                        sc.validate_non_empty()


class MetaCell(Cell):
    def __init__(self, spec: dict, info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.props: Dict[str, Any] = {
            'suggested': True,
            'error': True,
            'error_str': ""
        }
        self.val: str = ""
        position_in_base = 1 + self.info['position'] - self.spec['decpos']

        self.radix = RadixBase.name_to_base[self.spec['type']][position_in_base]
        self.ndigit = ndigit_for_radix(self.radix)

        self.parent: 'SuperCell'
        self.indexInParent: int

    def next_arg(self, arg: int, delta: int) -> 'MetaCell':
        return self.parent.next_arg(arg, delta).zones[self.indexInParent][0]

    def is_entry(self) -> bool:
        return self.info['type'] == 0

    def is_argument(self) -> bool:
        return self.info['type'] == 1

    def is_info(self) -> bool:
        return self.info['type'] == 2

    def is_first(self) -> bool:
        return self.info['position'] == 0

    def is_dec(self) -> bool:
        return self.info['position'] == self.spec['decpos'] - 1

    def is_last(self) -> bool:
        return self.info['position'] == self.spec['ncells'] - 1

    def add_zeros(self, val: Union[str, int, float]) -> str:
        val = str(val)
        while len(val) < self.ndigit:
            val = '0' + val
        return val

    def fill(self, content: Union[str, int, float], validated: bool):
        if content != '':
            content = self.add_zeros(content)
        else:
            content = str(content)
        # implement apply_fill if needed for difference table
        if self.info['root'].data is not None:
            self.info['root'].data[self.cartesian_coordinates[0]][self.cartesian_coordinates[1]] = content
        self.set_prop('error', False)
        self.set_prop('error_str', '')
        self.val = content
        if content == '':
            self.set_prop('suggested', True)
        elif validated:
            self.parent.on_touch_value(validated)

    def erase(self):
        self.fill('', False)

    def value(self) -> str:
        return self.val

    def set_prop(self, key: str, value: Any):
        self.props[key] = value

    def append_prop(self, key: str, value: Any):
        self.props[key].append(value)

    def on_touch_value(self):
        self.check_error()

    def check_error(self):
        if '*' in self.val:
            return
        if not self.is_first() and int(self.val) >= self.radix:
            self.set_prop('error', True)
            self.set_prop('error_str', 'Number too large for radix')
        else:
            self.set_prop('error', False)

    def validate(self):
        self.set_prop('suggested', False)
        self.check_error()


class SuperCell(Zone):
    def __init__(self, spec: dict, positions: List[Cartesian], info: Optional[dict] = None):
        super().__init__(info=info)
        self.spec = spec
        self.positions = positions
        index = 0
        for position in self.positions:
            self.add_zone(MetaCell(self.spec, {'position': index}), position)
            index += 1
        self.props: Dict[str, Any] = {
            'user_comment': ''
        }

        self.zones: List[Tuple[MetaCell, Cartesian]]  # type: ignore

    def next_arg(self, arg: int, delta: int) -> 'SuperCell':
        return self.parent.next_arg(arg, delta, self.indexInParent)  # type: ignore

    def is_entry(self) -> bool:
        return self.info['type'] == 0

    def is_argument(self) -> bool:
        return self.info['type'] == 1

    def is_info(self) -> bool:
        return self.info['type'] == 2

    def validate_non_empty(self):
        for zone, _ in self.zones:
            if zone.val != '':
                zone.validate()

    def test_full_prop(self, key: str, value: Any) -> bool:
        for zone, _ in self.zones:
            if zone.props[key] != value:
                return False
        return True

    def is_complete(self) -> bool:
        for zone, _ in self.zones:
            if zone.val == "":
                return False
            if '*' in zone.val:
                return False
        return True

    def test_at_least_one_prop(self, key: str, value: Any) -> bool:
        for zone, _ in self.zones:
            if zone.props[key] == value:
                return True
        return False

    def set_prop(self, key: str, value: Any):
        if key in self.props:
            self.props[key] = value
        else:
            for zone, _ in self.zones:
                zone.set_prop(key, value)

    def append_prop(self, key: str, value: Any):
        if key in self.props:
            self.props[key].append(value)
        else:
            for zone, _ in self.zones:
                zone.append_prop(key, value)

    def erase(self):
        for zone, _ in self.zones:
            zone.erase()

    def on_touch_value(self, validated: bool = None):
        if validated is not None:
            if validated:
                self.validate_non_empty()
            else:
                self.set_prop('suggested', True)

        for zone, _ in self.zones:
            zone.on_touch_value()

    def check_error(self):
        for zone, _ in self.zones:
            zone.check_error()

    def get_number_view(self) -> Optional[NumberView]:
        if not self.is_complete():
            return None
        leftList = []
        rightList = []
        for i in range(self.spec['decpos']):
            leftList.append(self.zones[i][0].val)
        sign = 1
        if len(leftList) > 0 and len(leftList[0]) > 1 and str(leftList[0])[:1] == '-':
            sign = -1
            leftList[0] = str(leftList[0])[1:]
        leftList.reverse()
        for i in range(self.spec['decpos'], self.spec['ncells']):
            rightList.append(self.zones[i][0].val)
        leftListNumber = [abs(int(n)) for n in leftList]
        rightListNumber = [abs(int(n)) for n in rightList]
        return RadixBase.name_to_base[self.spec['type']].type(leftListNumber, rightListNumber, remainder=0.0, sign=sign)

    def set_number_view(self, value: NumberView, validated: bool):
        name = self.spec['type']
        ndec = self.spec['decpos']
        nsig = self.spec['ncells'] - ndec
        if name != 'none':
            num = value.to_base(RadixBase.name_to_base[name], nsig + 1)
            num.round(nsig)

            if len(num.leftList) > ndec:
                tmp = copy.deepcopy(num.leftList)
                for i in range(ndec):
                    tmp[i] = 0
                sn = RadixBase.name_to_base[name].type(tmp, [], remainder=0, sign=1)
                factor = 1
                for i in range(ndec-1):
                    factor *= RadixBase.name_to_base[name].left[i]

                num.leftList[ndec-1] += int(sn) // factor

            for i in range(ndec):
                if ndec - 1 - i < len(num.leftList):
                    val = num.leftList[ndec - 1 - i]
                else:
                    val = 0
                self.zones[i][0].fill(str(val), validated)
            for i in range(nsig):
                self.zones[i+ndec][0].fill(str(num.rightList[i]), validated)
            if num.sign < 0:
                self.zones[0][0].fill('-' + self.zones[0][0].val, validated)
        else:
            self.zones[0][0].fill(float(value), validated)

        if validated:
            self.validate_non_empty()

    def next_nargs(self, arg: int, direction: int, nb: int = 1) -> List['SuperCell']:
        supercells = []
        counter = 0
        current = self
        while counter < nb:
            next = current.next_arg(arg, direction)
            if next is None:
                break
            if next.is_complete() and next.test_full_prop('suggested', False):
                supercells.append(next)
                counter += 1
            current = next
        return supercells

