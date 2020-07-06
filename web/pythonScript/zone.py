
from typing import Tuple, List, Optional, Any, Dict

Cartesian = Tuple[int, int]


class Zone:
    def __init__(self, info: Optional[dict] = None):
        self.info: dict
        if info is None:
            self.info = {}
        else:
            self.info = info
        self.R = 0
        self.C = 0
        self.cartesian_coordinates = (0, 0)
        self.zone_coordinates: List[int] = []
        self.zones: List[Tuple['Zone', Cartesian]] = []
        self.parent: Optional['Zone'] = None
        self.indexInParent: Optional[int] = None

    def has_inside(self, cartesian: Cartesian):
        for zone, position in self.zones:
            if zone.has_inside((cartesian[0] - position[0], cartesian[1] - position[1])):
                return True
        return False

    def get_zone(self, cartesian: Cartesian):
        for zone, position in self.zones:
            if zone.has_inside((cartesian[0] - position[0], cartesian[1] - position[1])):
                return zone
        return None

    def get_leaf(self, cartesian: Cartesian):
        zone = self.get_zone(cartesian)
        if zone is not None:
            return zone.get_leaf(cartesian)
        else:
            return None

    def add_zone(self, zone: 'Zone', position: Cartesian):
        self.zones.append((zone, position))
        self.R = max(self.R, position[0] + zone.R)
        self.C = max(self.C, position[1] + zone.C)
        zone.parent = self
        zone.indexInParent = len(self.zones) - 1

    def compute_dimensions(self):
        self.R = 0
        self.C = 0
        for zone, position in self.zones:
            self.R = max(self.R, position[0] + zone.R)
            self.C = max(self.C, position[1] + zone.C)

    def remove_zone(self, index: int):
        del self.zones[index]

    def from_path(self, zone_coordinates: List[int]):
        if len(zone_coordinates) == 0:
            return self
        else:
            return self.zones[zone_coordinates[0]][0].from_path(zone_coordinates[1:])
            # return self.zones[zone_coordinates[0]][0].from_path(zone_coordinates[-1])

    def fill_info(self, cartesian: Cartesian, zone_coordinates: List[int]):
        self.info: dict
        self.cartesian_coordinates = cartesian
        self.zone_coordinates = zone_coordinates
        index = 0
        for zone, position in self.zones:
            for key in self.info:
                zone.info[key] = self.info[key]
            zone.fill_info((cartesian[0] + position[0], cartesian[1] + position[1]), zone_coordinates + [index])
            index += 1


class MainZone(Zone):
    def __init__(self, info: Optional[dict] = None):
        super().__init__(info=info)
        self.info['root'] = self
        self.grid: List[List[Optional['Cell']]]
        self.super_grid: List[List[Optional['Cell']]]
        self.data: List[List[Optional[int]]]

    def build_grid(self):
        self.grid = []
        self.super_grid = []
        self.data = []
        for i in range(self.R):
            self.grid.append([])
            self.super_grid.append([])
            self.data.append([])
            for j in range(self.C):
                self.grid[i].append(None)
                self.super_grid[i].append(None)
                self.data[i].append("")

    def setup(self):
        self.compute_dimensions()
        self.build_grid()
        self.fill_info((0, 0), [])


class Cell(Zone):
    def __init__(self, info: Optional[dict] = None):
        super().__init__(info=info)
        self.R = 1
        self.C = 1
        self.val = ""

    def has_inside(self, cartesian: Cartesian):
        if cartesian[0] == 0 and cartesian[1] == 0:
            return True
        else:
            return False

    def get_leaf(self, cartesian: Cartesian):
        if cartesian[0] == 0 and cartesian[1] == 0:
            return self
        else:
            return None

    def get_zone(self, cartesian: Cartesian):
        raise ValueError('Calling get_zone on a leaf: forbidden')

    def add_zone(self, zone: Zone, position: Cartesian):
        raise ValueError('Calling add_zone on a leaf: forbidden')

    def fill_info(self, cartesian: Cartesian, zone_coordinates: List[int]):
        self.cartesian_coordinates = cartesian
        self.zone_coordinates = zone_coordinates
        self.info['root'].grid[self.cartesian_coordinates[0]][self.cartesian_coordinates[1]] = self
        self.info['root'].super_grid[self.cartesian_coordinates[0]][self.cartesian_coordinates[1]] = self.parent
