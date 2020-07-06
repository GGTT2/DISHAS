
import numpy as np
import conversion
from conversion import NumberView
from math import log


class SmartNumber():
    def __init__(self, value):
        self.tobases = {}
        self.decimal = None

        if type(value) == NumberView:
            self.initialbase = value.base
            self.tobases[self.initialbase] = value

        else:
            self.initialbase = None
            self.decimal = float(value)

    def compute_base(self, base=None, significant=None):
        if not significant:
            significant = 10
        # on souhaite convertir vers une base
        if base:
            if base in self.tobases:
                return self.tobases[base]
            else:
                if self.initialbase:
                    res = conversion.base_to_base(
                        self.tobases[self.initialbase],
                        base,
                        significant
                    )
                else:
                    res = conversion.dec_to_base(
                        self.decimal,
                        base,
                        significant
                    )
                self.tobases[base] = res
                return self.tobases[base]
        # on souhaite calculer une valeur decimale
        else:
            if self.initialbase:
                res = conversion.base_to_dec(self.tobases[self.initialbase])
            else:
                res = self.decimal
            self.decimal = res
            return self.decimal

    def compute_decimal(self):
        return self.compute_base()

    def purge(self):
        if not self.initialbase:
            self.tobases = {}
        else:
            inter = self.tobases[self.initialbase]
            self.tobase = {}
            self.tobases[self.initialbase] = inter
            self.decimal = None

    def as_base(self, base=None, significant=None):
        self.compute_base(base, significant)
        self.initialbase = base
        self.purge()
        return self

    def as_decimal(self):
        return self.as_base()

    def round(self, *args):
        if len(args) == 0:
            ndec = self.get_significant()
        else:
            ndec = args[0]
        if not self.initialbase:
            self.decimal = float((self.decimal * (10**ndec)) // (10**ndec))
        else:
            self.tobases[self.initialbase] = conversion.round(
                self.tobases[self.initialbase],
                ndec
            )
        self.purge()
        return self

    def get_value(self):
        if not self.initialbase:
            return self.decimal
        else:
            return self.tobases[self.initialbase]

    def get_significant(self):
        if self.initialbase:
            return len(self.get_value().nright)
        else:
            return int(log(2**54, 10))

    def to_json(self):
        if self.initialbase:
            return self.tobases[self.initialbase].to_json()
        else:
            return self.decimal

    def __str__(self):
        if self.initialbase:
            return str(self.tobases[self.initialbase])
        else:
            return str(self.decimal)


def SmartNumberFromJson(dic):
    return SmartNumber(NumberView(
        np.array(dic["nleft"], dtype=np.int64),
        np.array(dic["nright"], dtype=np.int64),
        dic["reminder"],
        dic["base"],
        dic["sign"]
    ))
