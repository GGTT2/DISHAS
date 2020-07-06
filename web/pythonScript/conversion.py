import numpy as np  # type: ignore
import copy
import gmpy  # type: ignore
import sys
from abc import ABCMeta
from memoized import memoized  # type: ignore
import math
import pdb  # type: ignore
import traceback
from tqdm import tqdm  # type: ignore
from fractions import Fraction
from typing import Union, Iterable, Optional, Type, Any, \
    TypeVar, Generic, List, Dict, Tuple, ClassVar, overload

"""
When performing tests on very precise numbers (For example sexagesimal with more than 7
fractional positions), avoid using floating number.
Use instead the Decimal NumberView class.

>>> Sexagesimal(20.1, 10)
20 ; 06,00,00,00,00,00,00,00,14,16

>>> Sexagesimal(Decimal("20.1"), 10)
20 ; 06,00,00,00,00,00,00,00,00,00

"""

current_module = sys.modules[__name__]

T = TypeVar('T')


class LoopingList(list, Generic[T]):
    """
    A class for lists looping to the right.
    If an index is queried outside the boundaries of the list, the last element is returned.

    >>> test = LoopingList([0, 1, 2])
    >>> test[0]
    0
    >>> test[2]
    2
    >>> test[45]
    2

    """

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)

    @overload
    def __getitem__(self, key: int) -> T:
        ...

    @overload
    def __getitem__(self, key: slice) -> List[T]:
        ...

    def __getitem__(self, key):
        if isinstance(key, slice):
            raise NotImplementedError
        if key >= len(self):
            return self.__getitem__(len(self) - 1)
        return super().__getitem__(key)

    def __str__(self) -> str:
        return super(LoopingList, self).__str__()[:-1] + ', ...]'

    def __repr__(self) -> str:
        return super(LoopingList, self).__repr__()[:-1] + ', ...]'


class RadixBase:
    """
    A class representing a numeral system. A radix must be specified at each position,
    by specifying an integer list for the integer positions, and an integer list for the
    fractional positions.
    """

    # This dictionary records all the instantiated RadixBase objects
    name_to_base: ClassVar[Dict[str, 'RadixBase']] = {}

    def __init__(self, left: Iterable[int], right: Iterable[int],
                 name: str, integer_separators: Optional[Iterable[str]] = None):
        """
        Definition of a numeral system. A radix must be specified for each integer position
        (left argument) and for each fractional position (right argument).
        Note that the integer position are counted from right to left (starting from the ';'
        symbol and going to the left).

        >>> historical_base = RadixBase([30, 12, 10], [60], 'historical', ['s ', 'r ', ''])

        :param left: Radix list for the integer part
        :param right: Radix list for the fractional part
        :param name: Name of this numeral system
        :param integer_separators: List of string separators, used for displaying the integer part of the number
        """
        self.left: LoopingList[int] = LoopingList(left)
        self.right: LoopingList[int] = LoopingList(right)
        self.name = name
        if integer_separators is not None:
            self.integer_separators: LoopingList[str] = LoopingList(integer_separators)
        else:
            self.integer_separators: LoopingList[str] = LoopingList([])
            for elm in left:
                if elm != 10:
                    self.integer_separators.append(',')
                else:
                    self.integer_separators.append('')

        # Record the new RadixBase
        RadixBase.name_to_base[self.name] = self

        # Build a class inheriting from NumberView, that will use this RadixBase as
        # its numeral system. This class is recorded in the module namespace with
        #  the specified name (converted in Camel Case)
        type_name = "".join(map(str.capitalize, self.name.split('_')))
        new_type = type(type_name, (NumberView,), {'base': self})
        setattr(current_module, type_name, new_type)

        # Store the newly created NumberView class
        self.type: Type[NumberView] = new_type

    def __getitem__(self, pos: int) -> int:
        """
        Return the radix at the specified position. Position 0 represents the last integer
        position before the fractional part (i.e. the position just before the ';' in sexagesimal
        notation, or just before the '.' in decimal notation). Positive positions represent
        the fractional positions, negative positions represent the integer positions.

        :param pos: Position. <= 0 for integer part (with 0 being the right-most integer position),
                    > 0 for fractional part
        :return: Radix at the specified position
        """
        if pos <= 0:
            return self.left[-pos]
        else:
            return self.right[pos - 1]

    @memoized
    def float_at_pos(self, pos):
        factor = 1.0
        if pos > 0:
            for i in range(pos):
                factor /= self.right[i]
            return factor
        elif pos == 0:
            return factor
        else:
            for i in range(-pos):
                factor *= self.left[i]
            return factor

    @memoized
    def mul_factor(self, i, j):
        numerator = 1
        for k in range(1, i + j + 1):
            numerator *= self[k]
        denom_i = 1
        for k in range(1, i + 1):
            denom_i *= self[k]
        denom_j = 1
        for k in range(1, j + 1):
            denom_j *= self[k]
        if numerator % (denom_i * denom_j) == 0:
            return numerator // (denom_i * denom_j)
        return numerator / (denom_i * denom_j)


def ndigit_for_radix(radix: int) -> int:
    """
    Compute how many ten-radix digits are needed to represent a position of
    the specified radix.

    >>> ndigit_for_radix(10)
    1
    >>> ndigit_for_radix(60)
    2

    :param radix:
    :return:
    """
    return int(np.ceil(np.log10(radix)))


class NumberViewException(Exception):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)


class EmptyStringException(NumberViewException):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)


class TooManySeparators(NumberViewException):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)


class TypeMismatch(NumberViewException):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)


class NumberView(metaclass=ABCMeta):
    """
    Abstract class allowing to represent a value in a specific RadixBase.
    Each time a new RadixBase object is recorded, a new class inheriting from NumberView
    is created and recorded in the module namespace.
    The RadixBase to be used will be placed in the class attribute 'base'

    Attributes:
        leftList        The list of values at integer positions (from right to left)
        rightList       The list of values at fractional positions
        remainder       When a computation requires more precision than the precision
                            of this number, we store a floating remainder to keep track of it
        sign            The sign of this number

    Class attributes:
        base            A RadixBase object (will be attributed dynamically to the children inheriting this class)

    >>> import conversion
    >>> test = conversion.RadixBase([60], [60], 'test_sexa')
    >>> test.type == conversion.TestSexa
    True
    >>> issubclass(conversion.TestSexa, conversion.NumberView)
    True
    >>> isinstance(conversion.TestSexa.base, conversion.RadixBase)
    True

    """

    base: RadixBase

    def __init__(self, *args, remainder: float = 0.0, sign: int = 1):
        """
        There are 4 ways of creating a NumberView (some of them need that you specify
        a number of significant positions)

        - From a python number (float or int)
        >>> Sexagesimal(64.55, 3)
        01,04 ; 33,00,00

        - From 2 lists of integers (a list of values for integer positions, a list of
                values for fractional positions)
        >>> Sexagesimal([4, 1], [33, 0, 0])
        01,04 ; 33,00,00

        - From a string representation of the value
        >>> Sexagesimal("01,04 ; 33,00,00")
        01,04 ; 33,00,00

        - From another NumberView object
        >>> hist_val = HistoricalDecimal('64; 55')
        >>> Sexagesimal(hist_val, 3)
        01,04 ; 33,00,00

        :param args: Arguments containing a representation of the number.
        :param remainder: Since we have a limited number of fractional positions for our
            NumberView object, a float reminder is used when performing computations
            to prevent loss of precision.
        :param sign: Sign of the number
        """
        if len(args) == 2:
            if isinstance(args[0], NumberView):
                res = args[0].to_base(self.base, args[1])
                self.leftList: List[int] = res.leftList
                self.rightList: List[int] = res.rightList
                self.remainder: float = res.remainder
                self.sign: int = res.sign
            elif isinstance(args[0], float) or isinstance(args[0], int):
                res = type(self).float64_to_base(args[0], args[1])
                self.leftList: List[int] = res.leftList
                self.rightList: List[int] = res.rightList
                self.remainder: float = res.remainder
                self.sign: int = res.sign
            elif isinstance(args[0], list):
                self.leftList: List[int] = args[0]
                self.rightList: List[int] = args[1]
                self.remainder: float = remainder
                self.sign: int = sign
            else:
                raise ValueError("Incorrect parameters at NumberView creation")
        elif len(args) == 1:
            if isinstance(args[0], str):
                res = type(self).from_string(args[0])
                self.leftList: List[int] = res.leftList
                self.rightList: List[int] = res.rightList
                self.remainder: float = res.remainder
                self.sign: int = res.sign
            else:
                if isinstance(args[0], NumberView) or \
                        isinstance(args[0], float) or isinstance(args[0], int):
                    raise ValueError("Please specify a number of significant positions")
                raise ValueError("Incorrect parameters at NumberView creation")

        elif len(args) == 0:
            raise ValueError("Incorrect number of parameter at NumberView creation")

        if len(self.leftList) == 0 and len(self.rightList) == 0:
            self.leftList = [0]

    def get_significant_places(self) -> int:
        """
        :return: the number of significant positions of this number
        """
        return len(self.rightList)

    def to_fraction(self) -> Fraction:
        """
        :return: this NumberView as a Fraction object.
        The remainder is NOT taken into account
        """
        num = abs(int(self))
        denom = 1
        for i in range(len(self.rightList)):
            num *= self.base.right[i]
            denom *= self.base.right[i]
            num += self.rightList[i]
        return Fraction(self.sign * num, denom)

    @classmethod
    def from_fraction(cls, fraction: Fraction, remainder: float = 0.0,
                      significant: Optional[int] = None) -> 'NumberView':
        """
        :param fraction: a Fraction object
        :param remainder: remainder to be added
        :param significant: signifcant precision desired
        :return: a NumberView object computed from a Fraction
        """
        numerator = fraction.numerator
        denominator = fraction.denominator
        if numerator > 0:
            sign = 1
        elif numerator < 0:
            sign = -1
        elif remainder > 0:
            sign = 1
        else:
            sign = 0
        res: NumberView = cls.int_to_base(abs(numerator) // abs(denominator),
                                              significant=0)
        res.sign = sign
        num = abs(numerator) % abs(denominator)
        denom = abs(denominator)

        res.remainder = remainder

        if denom == 1:
            if significant is not None:
                while len(res.rightList) < significant:
                    res.rightList.append(0)
            return res

        factor = 1

        max_iterations = significant
        if max_iterations is None:
            max_iterations = 100

        for i in range(max_iterations):
            factor *= cls.base.right[i]
            if factor % denom == 0:
                # we found the last significant position
                res.rightList.append(num * (factor // denom))
                break
            else:
                res.rightList.append(0)
        else:
            # here we temporarily store floats inside our NumberView object
            # this breaks typing but it is more handy
            res.rightList[-1] = num * (factor/denom)  # type: ignore
            res.__sanitize_float()

        if significant is not None:
            while len(res.rightList) < significant:
                res.rightList.append(0)

        res.__sanitize()
        return res

    def __str__(self) -> str:
        """
        Convert to string representation.
        Note that this representation is rounded (with respect to the
         remainder attribute) not truncated

        >>> str(Historical(1125.745, 4))
        '3r 01s 15 ; 44,42,00,00'

        :return: String representation of this number
        """
        if self.remainder >= 0.5:
            nv = self.rounded()
        else:
            nv = self
        res = ''
        if nv.sign < 0:
            res += '-'

        for i in reversed(range(len(nv.leftList))):
            num = str(nv.leftList[i])
            digit = ndigit_for_radix(nv.base.left[i])
            while len(num) < digit:
                num = '0' + num
            res += num
            if i > 0:
                res += nv.base.integer_separators[i - 1]

        if nv.base.name != 'decimal':
            res += ' ; '
        else:
            res += ' . '

        for i in range(len(nv.rightList)):
            num = str(nv.rightList[i])
            digit = ndigit_for_radix(nv.base.right[i])
            while len(num) < digit:
                num = '0' + num
            res += num

            if i < len(nv.rightList) - 1:
                if nv.base.name != 'decimal':
                    res += ','
                else:
                    res += ''

        return res

    __repr__ = __str__

    @classmethod
    def from_string(cls, string: str) -> 'NumberView':
        """
        Class method to instantiate a NumberView object from a string

        >>> Sexagesimal('1, 12; 4, 25')
        01,12 ; 04,25
        >>> Historical('2r 7s 29; 45, 2')
        2r 07s 29 ; 45,02
        >>> Sexagesimal('0 ; 4, 45')
         ; 04,45
        >>> Sexagesimal('1;') - Sexagesimal('1 ; 4, 45')
        - ; 04,45

        :param string: String representation of the number
        :return: a new instance of NumberView
        """
        base = cls.base
        string = string.strip().lower()
        if len(string) == 0:
            raise EmptyStringException('String is empty')
        if string[0] == '-':
            sign = -1
            string = string[1:]
        else:
            sign = 1
        if base.name != 'decimal':
            left_right = string.split(';')
        else:
            left_right = string.split('.')
        if len(left_right) < 2:
            left = left_right[0]
            right = ''
        elif len(left_right) == 2:
            left = left_right[0]
            right = left_right[1]
        else:
            raise TooManySeparators('Too many separators in string')

        left = left.strip()
        right = right.strip()

        left_numbers = []
        right_numbers = []

        if len(right) > 0:
            if base.name == 'decimal':
                # right_number_string = right.split('')
                right_number_string = list(right)
            else:
                right_number_string = right.split(',')
            for i in range(len(right_number_string)):
                right_numbers.append(int(right_number_string[i]))

        if len(left) > 0:
            for i in range(len(base.integer_separators)):
                separator = base.integer_separators[i].strip().lower()
                if separator != '':
                    left_number_string = left.split(separator)
                else:
                    left_number_string = list(left)
                left_numbers.append(int(left_number_string[-1]))
                if len(left_number_string) < 2:
                    left = ""
                    break
                left = base.integer_separators[i].join(left_number_string[:-1])

        if len(left) > 0:
            if base.left[-1] == 10:
                # left_number_string = left.split('')
                left_number_string = list(left)
            else:
                left_number_string = left.split(',')
            for i in range(len(left_number_string)):
                left_numbers.append(int(left_number_string[i].strip()))

        res: 'NumberView' = cls(left_numbers, right_numbers, remainder=0.0, sign=sign)
        return res.__sanitize()

    def __deepcopy__(self, memodict : Optional[dict] = {}) -> 'NumberView':
        """
        Allow tu use copy.deepcopy on a NumberView object

        >>> import copy
        >>> n1 = Sexagesimal(122.1233111, 7)
        >>> n2 = copy.deepcopy(n1)
        >>> n2.truncate(3)
        02,02 ; 07,23,55
        >>> n1
        02,02 ; 07,23,55,11,51,21,36

        :param memodict:
        :return: deepcopy of this NumberView object
        """
        return type(self)(
            copy.deepcopy(self.leftList),
            copy.deepcopy(self.rightList),
            remainder=self.remainder,
            sign=self.sign,
        )

    def resize(self, significant: int):
        """
        Resize this NumberView object to the specified precision

        >>> n = Sexagesimal('02, 02; 07, 23, 55, 11, 51, 21, 36')
        >>> n
        02,02 ; 07,23,55,11,51,21,36
        >>> n.remainder
        0.0
        >>> n.resize(4)
        02,02 ; 07,23,55,12
        >>> n.remainder
        0.856
        >>> n.resize(7)
        02,02 ; 07,23,55,11,51,21,36

        :param significant: Number of desired significant positions
        :return: self
        """
        if significant == len(self.rightList):
            return self
        factor = 1.0
        for i in range(len(self.rightList)):
            factor /= self.base.right[i]
        remainderValue = factor * self.remainder

        if significant > len(self.rightList):
            self.remainder = 0.0
            for i in range(significant - len(self.rightList)):
                self.rightList.append(0)
            self.__iadd__(type(self).float64_to_base(self.sign * remainderValue, significant))
        else:
            factor = 1.0
            for i in range(significant):
                factor /= self.base.right[i]
            for i in range(significant, len(self.rightList)):
                factor /= self.base.right[i]
                remainderValue += factor * self.rightList[i]

            self.truncate(significant)
            self.__iadd__(type(self).float64_to_base(self.sign * remainderValue, significant))
        return self

    def __simplify_integer_part(self):
        """
        Remove the useless trailing zeros in the integer part
        :return: self
        """
        count = 0
        for i in range(len(self.leftList) - 1, -1, -1):
            if self.leftList[i] != 0:
                break
            count += 1
        if count > 0:
            self.leftList = self.leftList[:-count]
        return self

    def truncate(self, n: int):
        """
        Truncate this NumberView object to the specified precision

        >>> n = Sexagesimal('02, 02; 07, 23, 55, 11, 51, 21, 36')
        >>> n
        02,02 ; 07,23,55,11,51,21,36
        >>> n.truncate(3)
        02,02 ; 07,23,55
        >>> n.resize(7)
        02,02 ; 07,23,55,00,00,00,00

        :param n: Number of desired significant positions
        :return:
        """
        if n > len(self.rightList):
            return self
        self.remainder = 0.0
        self.rightList = self.rightList[:n]
        return self

    def round(self, significant: Optional[int] = None):
        """
        Round this NumberView object to the specified precision.
        If no precision is specified, the rounding is performed with respect to the
        remainder attribute.

        >>> n = Sexagesimal('02, 02; 07, 23, 55, 11, 51, 21, 36')
        >>> n
        02,02 ; 07,23,55,11,51,21,36
        >>> n.round(4)
        02,02 ; 07,23,55,12
        >>> n.resize(7)
        02,02 ; 07,23,55,12,00,00,00

        :param significant: Number of desired significant positions
        :return: self
        """
        if significant is None:
            significant = len(self.rightList)
        self.resize(significant)
        if self.remainder > 0.5:
            if significant != 0 or len(self.leftList) > 0:
                self[significant] += 1
        self.remainder = 0.0
        return self.__sanitize()

    def rounded(self, significant : Optional[int] = None) -> 'NumberView':
        """
        Return a rounded copy of this NumberView

        >>> n = Sexagesimal('02, 02; 07, 23, 55, 11, 51, 21, 36')
        >>> n
        02,02 ; 07,23,55,11,51,21,36
        >>> n.rounded(4)
        02,02 ; 07,23,55,12
        >>> n
        02,02 ; 07,23,55,11,51,21,36

        :param significant: Number of desired significant positions
        :return:
        """
        return copy.deepcopy(self).round(significant=significant)

    def resized(self, significant: int) -> 'NumberView':
        return copy.deepcopy(self).resize(significant=significant)

    def __getitem__(self, pos):
        """
        Allow to get a specific position value of this NumberView object
        by specifying an index. The position 0 corresponds to the right-most integer position.
        Negative positions correspond to the other integer positions, positive
        positions correspond to the fractional positions.

        >>> n = Sexagesimal('02, 10; 07, 23, 55, 11, 51, 21, 36')
        >>> n
        02,10 ; 07,23,55,11,51,21,36
        >>> n[0]
        10
        >>> n[-1]
        2
        >>> n[1]
        7
        >>> n[3]
        55

        :param pos: desired index
        :return: value at the specified position
        """
        if pos <= 0:
            return self.leftList[-pos]
        else:
            return self.rightList[pos - 1]

    def __setitem__(self, pos: int, value: int):
        """
        Allow to modify a specific position value of this NumberView object
        by specifying an index and a new value. The position 0 corresponds to the
        right-most integer position.
        Negative positions correspond to the other integer positions, positive
        positions correspond to the fractional positions.

        >>> n = Sexagesimal('02, 10; 07, 23, 55, 11, 51, 21, 36')
        >>> n
        02,10 ; 07,23,55,11,51,21,36
        >>> n[0] = 3
        >>> n
        02,03 ; 07,23,55,11,51,21,36
        >>> n[-1] = 17
        >>> n
        17,03 ; 07,23,55,11,51,21,36
        >>> n[1] = 45
        >>> n
        17,03 ; 45,23,55,11,51,21,36
        >>> n[3] = 30
        >>> n
        17,03 ; 45,23,30,11,51,21,36
        """
        if pos <= 0:
            self.leftList[-pos] = value
        else:
            self.rightList[pos - 1] = value

    def __sanitize(self, pos: Optional[int] = None):
        self.__sanitize_helper(pos)
        self.__sanitize_remainder()
        # here we treat a rare particular case
        if len(self.leftList) > 0 and self.leftList[-1] < 0\
                or (len(self.leftList) == 0 and self.rightList[0] < 0):
            self.__sanitize_helper(pos)
            self.__sanitize_remainder()
        return self

    def __sanitize_remainder(self):
        pos = len(self.rightList)
        if len(self.leftList) == 0 and pos == 0:
            self.leftList.append(0)
        self[pos] += int(self.remainder)
        self.remainder -= int(self.remainder)
        if self.remainder < 0:
            self[pos] -= 1
            self.remainder += 1.0

        return self

    def __sanitize_helper(self, pos: Optional[int] = None):
        if pos is None:
            pos = len(self.rightList)

        self.__naive_sanitize()

        if (len(self.leftList) > 0 and self.leftList[-1] < 0)\
                or (len(self.leftList) == 0 and len(self.rightList) > 0 and self.rightList[0] < 0):
            for i in range(len(self.leftList)):
                self.leftList[i] *= -1
            for i in range(len(self.rightList)):
                self.rightList[i] *= -1
            self.remainder *= -1
            self.__naive_sanitize()
            self.sign *= -1

        self.__simplify_integer_part()

        return self

    def __naive_sanitize(self, pos: Optional[int] = None):
        if pos is None:
            pos = len(self.rightList)
        if pos <= 0 and -pos >= len(self.leftList):
            return self
        if self[pos] >= self.base[pos]:
            factor = self[pos] // self.base[pos]
            self[pos] = self[pos] % self.base[pos]
            if pos - 1 <= 0 and -(pos - 1) >= len(self.leftList):
                self.leftList.append(0)
            self[pos - 1] += factor
        if self[pos] < 0:
            if pos - 1 <= 0 and -(pos - 1) >= len(self.leftList):
                return self
            factor = -(1 + (-self[pos]) // self.base[pos])
            modulo = self.base[pos] - ((-self[pos]) % self.base[pos])
            if modulo == self.base[pos]:
                modulo = 0
                factor += 1
            self[pos] = modulo
            self[pos - 1] += factor
        return self.__naive_sanitize(pos - 1)

    @classmethod
    def float64_to_base(cls, floa: float, significant: int) -> 'NumberView':
        """
        Class method to produce a new NumberView object from a floating number

        >>> Sexagesimal(0.33333333333, 4)
         ; 20,00,00,00

        :param floa: floating value of the number
        :param significant: precision of the number
        :return: a new NumberView object
        """
        base = cls.base
        sign = int(np.sign(floa))
        floa *= sign

        pos = 0
        max_integer = 1

        while floa >= max_integer:
            max_integer *= base.left[pos]
            pos += 1

        left = [0] * pos
        right = [0] * significant

        int_factor = max_integer

        for i in range(pos - 1, -1, -1):
            int_factor //= base.left[i]
            position_value = int(floa / int_factor)
            floa -= position_value * int_factor
            left[i] = position_value

        factor = 1.0
        for i in range(significant):
            factor /= base.right[i]
            position_value = int(floa / factor)
            floa -= position_value * factor
            right[i] = position_value

        remainder = floa / factor
        return cls(left, right, remainder=remainder, sign=sign)

    @classmethod
    def zero(cls, significant: int) -> 'NumberView':
        """
        Class method to produce a zero number of the specified precision

        >>> Sexagesimal.zero(7)
         ; 00,00,00,00,00,00,00

        :param significant: desired precision
        :return: a zero number
        """
        return cls([], [0] * significant, remainder=0.0, sign=1)

    @classmethod
    def one(cls, significant: int) -> 'NumberView':
        """
        Class method to produce a unit number of the specified precision

        >>> Sexagesimal.one(5)
        01 ; 00,00,00,00,00

        :param significant: desired precision
        :return: a unit number
        """
        return cls([1], [0] * significant, remainder=0.0, sign=1)

    @classmethod
    def int_to_base(cls, value: int, significant: int) -> 'NumberView':
        """
        Class method to produce a new NumberView object from an integer number

        >>> Sexagesimal(12, 4)
        12 ; 00,00,00,00

        :param value: integer value of the number
        :param significant: precision of the number
        :return: a new NumberView object
        """
        base = cls.base
        sign = int(np.sign(value))
        value *= sign

        pos = 0
        max_integer = 1

        while value >= max_integer:
            max_integer *= base.left[pos]
            pos += 1

        left = [0] * pos
        right = [0] * significant

        factor = max_integer

        for i in range(pos - 1, -1, -1):
            factor //= base.left[i]
            position_value = int(value / factor)
            value -= position_value * factor
            left[i] = position_value

        remainder = 0.0
        return cls(left, right, remainder=remainder, sign=sign)

    def __float__(self) -> float:
        """
        Compute the float value of this NumberView object

        >>> float(Sexagesimal('01;20,00'))
        1.3333333333333333
        >>> float(Sexagesimal('14;30,00'))
        14.5

        :return: float representation of this NumberView object
        """
        floa = 0.0
        factor = 1.0
        for i in range(len(self.leftList)):
            floa += factor * self.leftList[i]
            factor *= self.base.left[i]
        factor = 1.0
        for i in range(len(self.rightList)):
            factor /= self.base.right[i]
            floa += factor * self.rightList[i]

        floa += factor * self.remainder
        return float(floa * self.sign)

    def __int__(self) -> int:
        """
        Compute the integer value of this NumberView object

        >>> int(Sexagesimal('01;20,00'))
        1
        >>> int(Sexagesimal('14;30,00'))
        14

        :return: integer representation of this NumberView object
        """
        num = 0
        factor = 1
        for i in range(len(self.leftList)):
            num += factor * self.leftList[i]
            factor *= self.base.left[i]
        return int(num * self.sign)

    @staticmethod
    def __fractionnal_position_base_to_base(value: int, pos: int, base1: RadixBase,
                                            base2: RadixBase, significant: int) -> 'NumberView':
        left = [0]
        right = [0] * significant

        denom = gmpy.mpz(1)
        for i in range(pos + 1):
            denom *= gmpy.mpz(base1.right[i])

        num = gmpy.mpz(value)
        rem = 0
        for i in range(significant):
            num *= gmpy.mpz(base2.right[i])
            quo = num // denom
            rem = num % denom
            right[i] = int(quo)
            num = rem

        # remainder = 0.0
        gros_rem = (gmpy.mpz(1000000000) * rem) // denom
        remainder = int(gros_rem) / 1000000000

        return base2.type(left, right, remainder=remainder, sign=1)

    def to_base(self, base: RadixBase, significant: int) -> 'NumberView':
        """
        Convert this number to the specified base

        >>> a = Sexagesimal('0; 20, 00, 00')
        >>> Decimal(a, 7)
         . 3333333

        :param base: a RadixBase object
        :param significant: the precision of the result
        :return: a new NumberView object
        """
        if self.sign == 0:
            self.sign = 1

        res = base.type.int_to_base(self.sign * int(self), significant)

        for i in range(len(self.rightList)):
            # a = self.__fractionnal_position_base_to_base(self.rightList[i], i, self.base, base, significant)
            res += self.__fractionnal_position_base_to_base(self.rightList[i], i, self.base, base, significant)

        factor = 1.0
        for i in range(len(self.rightList)):
            factor /= self.base.right[i]
        remainderValue = factor * self.remainder

        res += base.type.float64_to_base(remainderValue, significant)
        res.sign = self.sign
        return res

    @staticmethod
    def __sign_product(s1: int, s2: int) -> int:
        """
        Ensure a non zero sign product
        :param s1:
        :param s2:
        :return:
        """
        if s1 == 0:
            s1 = 1
        if s2 == 0:
            s2 = 1
        return s1 * s2

    def __iadd_helper(self, o: 'NumberView', sign:int = 1) -> 'NumberView':
        if not isinstance(self, type(o)) or not isinstance(o, type(self)):
            raise TypeMismatch('Conversion needed for operation between %s and %s' % (str(type(self)), str(type(o))))
        other = copy.deepcopy(o)
        if sign < 0:
            other.sign *= -1
        if len(self.rightList) < len(other.rightList):
            self.resize(len(other.rightList))
        if len(self.rightList) > len(other.rightList):
            other.resize(len(self.rightList))
        while len(self.leftList) < len(other.leftList):
            self.leftList.append(0)
        while len(self.leftList) > len(other.leftList):
            other.leftList.append(0)
        if self.sign == 0:
            self.sign = other.sign

        for pos in range(1 - len(self.leftList), len(self.rightList) + 1):
            self[pos] = self[pos] + self.__sign_product(self.sign, other.sign) * other[pos]

        self.remainder += self.__sign_product(self.sign, other.sign) * other.remainder

        self.__sanitize()

        return self

    def __mul_helper(self, other: 'NumberView') -> 'NumberView':
        if not isinstance(self, type(other)) or not isinstance(other, type(self)):
            raise TypeMismatch(
                'Conversion needed for operation between %s and %s' % (str(type(self)), str(type(other))))

        significant = len(self.rightList) + len(other.rightList)

        self_remainder_value = self.remainder * self.base.float_at_pos(len(self.rightList))
        other_remainder_value = other.remainder * other.base.float_at_pos(len(other.rightList))
        self_bak = copy.deepcopy(self)
        other_bak = copy.deepcopy(other)
        self_bak.remainder = 0.0
        other_bak.remainder = 0.0
        self_bak.sign = 1
        other_bak.sign = 1

        int_first = abs(int(self))
        int_second = abs(int(other))
        res: NumberView = type(self).int_to_base(int_first * int_second, significant)
        res.sign = 1
        for first in range(0, len(self.rightList) + 1):
            for second in range(0, len(other.rightList) + 1):
                if first == 0 and second == 0:
                    continue
                if first == 0:
                    res[second] += int_first * other[second]
                    continue
                if second == 0:
                    res[first] += self[first] * int_second
                    continue
                res[first + second] += self[first] * other[second] * self.base.mul_factor(first, second)

        if self.__contains_float():
            self.__sanitize_float()

        self_bak.__float_imul(other_remainder_value)
        other_bak.__float_imul(self_remainder_value)

        res += self_bak
        res += other_bak

        res.remainder += self.base.mul_factor(len(self.rightList), len(other.rightList)) * self.remainder * other.remainder

        res.sign = self.sign * other.sign

        if res.__contains_float():
            res.__sanitize_float()
        return res.__sanitize()

    def euclidian_div(self, other: 'NumberView') -> 'Tuple[NumberView, NumberView]':
        """
        Perform a euclidian division of this NumberObject by another

        >>> a = Sexagesimal('13; 50, 12')
        >>> b = Sexagesimal('3; 00, 00')
        >>> q, r = a.euclidian_div(b)
        >>> q
        04 ; 00,00
        >>> r
        01 ; 50,12,00,00
        >>> q * b + r == a
        True

        :param other: The other NumberView Object
        :return: a couple (quotient, remainder)
        """
        """
        q_sign = self.__sign_product(self.sign, other.sign)
        r_sign = self.sign

        s_sign = self.sign
        o_sign = other.sign

        self.sign = 1
        other.sign = 1
        """

        if not isinstance(self, type(other)) or not isinstance(other, type(self)):
            raise TypeMismatch(
                'Conversion needed for operation between %s and %s' % (str(type(self)), str(type(other))))
        if self < other:
            return type(self).zero(self.get_significant_places()), self
        if self == other:
            return type(self).one(self.get_significant_places()), type(self).zero(self.get_significant_places())
        num = gmpy.mpz(abs(int(self)))
        denom = gmpy.mpz(abs(int(other)))
        num_nv = self
        denom_nv = other

        if abs(int(denom_nv)) == 0:
            if denom_nv.get_significant_places() == 0:
                denom_nv = copy.deepcopy(denom_nv).resize(1)
            for i in range(len(denom_nv.rightList)):
                if denom_nv.rightList[i] != 0:
                    break
            else:
                if denom_nv.remainder == 0.0:
                    raise ZeroDivisionError
                for i in range(2, 20):
                    if denom_nv.rightList[-1] == 0:
                        denom_nv.resize(i)
                    else:
                        break
                else:
                    raise ValueError(
                        'You are performing an unsupported division (denominator too small)'
                    )
        if len(self.rightList) < len(denom_nv.rightList):
            num_nv = copy.deepcopy(self)
            num_nv.resize(denom_nv.get_significant_places())
        for pos in range(1, denom_nv.get_significant_places() + 1):
            num *= self.base[pos]
            num += num_nv[pos]
            denom *= self.base[pos]
            denom += denom_nv[pos]
        try:
            q_res: 'NumberView' = type(self).int_to_base(int(num // denom),
                                           num_nv.get_significant_places())
        except ZeroDivisionError:
            Warning('Max precision reached')
            q_res = type(self).int_to_base(0, num_nv.get_significant_places())
        r_res = num_nv - (q_res * denom_nv)
        q_res.__sanitize()
        r_res.__sanitize()
        return q_res, r_res

    def __int_imul(self, n: int) -> 'NumberView':
        for i in range(-len(self.leftList) + 1, len(self.rightList) + 1):
            self[i] *= n
        self.remainder *= n
        self.__sanitize()
        return self

    def __contains_float(self) -> bool:
        for i in range(-len(self.leftList) + 1, len(self.rightList) + 1):
            if not isinstance(self[i], int):
                return True
        return False

    def __sanitize_float(self) -> 'NumberView':
        for i in range(-len(self.leftList) + 1, len(self.rightList) + 1):
            frac, whole = math.modf(self[i])
            self[i] = int(whole)
            if i != len(self.rightList):
                self[i + 1] += frac * self.base[i + 1]
            else:
                self.remainder += frac
        return self

    def __float_imul(self, f: float) -> 'NumberView':
        for i in range(-len(self.leftList) + 1, len(self.rightList) + 1):
            self[i] *= f
        self.remainder *= f
        for i in range(-len(self.leftList) + 1, len(self.rightList) + 1):
            frac, whole = math.modf(self[i])
            self[i] = int(whole)
            if i != len(self.rightList):
                self[i + 1] += frac * self.base[i + 1]
            else:
                self.remainder += frac
        self.__sanitize()
        return self

    def __int_idiv(self, n: int, significant: Optional[int] = None) -> 'NumberView':
        if significant:
            self.resize(significant)
        self.remainder /= n
        for i in range(-len(self.leftList) + 1, len(self.rightList) + 1):
            q = self[i] // n
            r = self[i] % n
            self[i] = q
            if i != len(self.rightList):
                self[i + 1] += r * self.base[i + 1]
            else:
                self.remainder += r * self.base[i + 1] / (self.base[i] * n)
        self.__sanitize()
        return self

    def division(self, other: 'NumberView', significant: int) -> 'NumberView':
        """
        Divide this NumberView object with another

        :param other: the other NumberView object
        :param significant: the number of desired significant positions
        :return: the division of the two NumberView objects
        """
        if not isinstance(self, type(other)) or not isinstance(other, type(self)):
            raise TypeMismatch(
                'Conversion needed for operation between %s and %s' % (str(type(self)), str(type(other))))

        final_sign = self.__sign_product(self.sign, other.sign)

        num_nv = copy.deepcopy(self)
        denom_nv = copy.deepcopy(other)
        num_nv.sign = 1
        denom_nv.sign = 1

        q_res = self.zero(significant)

        multiplier = 1

        for i in range(significant + 1):
            q, r = num_nv.euclidian_div(denom_nv)
            q.__int_idiv(multiplier, significant=significant)
            q_res += q

            r.__int_imul(self.base.right[i])
            multiplier *= self.base.right[i]

            num_nv = r

        q_res.remainder += (float(num_nv) / float(denom_nv)) / self.base.right[i]
        q_res.sign = final_sign

        return q_res

    def set_from_other(self, other: 'NumberView'):
        self.leftList = other.leftList
        self.rightList = other.rightList
        self.remainder = other.remainder
        self.sign = other.sign

    def __iadd__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the iadd += operator

        >>> a = Sexagesimal('01, 21; 47, 25')
        >>> a
        01,21 ; 47,25
        >>> a += Sexagesimal('45; 32, 14, 22')
        >>> a
        02,07 ; 19,39,22

        :param other: other NumberView Object
        :return: self
        """
        return self.__iadd_helper(other)

    def __add__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the add + operator

        >>> Sexagesimal('01, 21; 47, 25') + Sexagesimal('45; 32, 14, 22')
        02,07 ; 19,39,22

        :param other:
        :return: the sum of the two NumberView objects
        """
        res = copy.deepcopy(self)
        res += other
        return res

    def __isub__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the isub -= operator

        >>> a = Sexagesimal('01, 21; 47, 25')
        >>> a
        01,21 ; 47,25
        >>> a -= Sexagesimal('45; 32, 14, 22')
        >>> a
        36 ; 15,10,38

        :param other: other NumberView Object
        :return: self
        """
        return self.__iadd_helper(other, sign=-1)

    def __sub__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the sub - operator

        >>> Sexagesimal('45; 32, 14, 22') - Sexagesimal('01, 21; 47, 25')
        -36 ; 15,10,38

        :param other: other NumberView Object
        :return: the substraction of self with the other NumberView object
        """
        res = copy.deepcopy(self)
        res -= other
        return res

    def __neg__(self) -> 'NumberView':
        """
        Implementation of the neg operator

        >>> -Sexagesimal('-12; 14, 15')
        12 ; 14,15

        :return: the opposite of self
        """
        res = copy.deepcopy(self)
        res.sign *= -1
        return res

    def __pos__(self) -> 'NumberView':
        """
        Implementation of the pos operator.
        Just return a deep copy of self

        >>> +Sexagesimal('-12; 14, 15')
        -12 ; 14,15

        :return: a deepcopy of self
        """
        return copy.deepcopy(self)

    def __abs__(self) -> 'NumberView':
        """
        Implementation of the abs operator.

        >>> abs(Sexagesimal('-12; 14, 15'))
        12 ; 14,15

        :return: the absolute value of self
        """
        if self.sign >= 0:
            return copy.deepcopy(self)
        return self.__neg__()

    def __mul__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the multiplication operator

        >>> Sexagesimal('01, 12; 04, 17') * Sexagesimal('7; 45, 55')
        09,19 ; 39,15,40,35

        :param other: The other NumberView to multiply
        :return: The product of the 2 NumberView object
        """
        return self.__mul_helper(other)

    def __imul__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the imul *= operator

        >>> a = Sexagesimal('01, 12; 04, 17')
        >>> a *= Sexagesimal('7; 45, 55')
        >>> a
        09,19 ; 39,15,40,35

        :param other: the other NumberView object to multiply
        :return: self
        """
        res = self.__mul_helper(other)
        self.set_from_other(res)
        return self

    def __floordiv__(self, other: 'NumberView') -> 'NumberView':
        """
        Return the quotient in the euclidian division of self with other

        >>> Sexagesimal('01, 12; 04, 17') // Sexagesimal('7; 45, 55')
        09 ; 00,00

        :param other: the other NumberView object
        :return: the quotient in the euclidian division of self with other
        """
        return self.euclidian_div(other)[0]

    def __mod__(self, other: 'NumberView') -> 'NumberView':
        """
        Return the remainder in the euclidian division of self with other

        >>> Sexagesimal('01, 12; 04, 17') % Sexagesimal('7; 45, 55')
        02 ; 11,02,00,00

        :param other: the other NumberView object
        :return: the remainder in the euclidian division of self with other
        """
        return self.euclidian_div(other)[1]

    def __truediv__(self, other: 'NumberView') -> 'NumberView':
        """
        Return the division of self with other. NB: To select the precision of
        the result (i.e. its number of significant positions) you should use the
        division method.

        TODO investigate the precision of this division before writing unitary tests

        :param other: the other NumberView object
        :return: the division of self with other
        """
        return self.division(other, max(len(self.rightList), len(other.rightList)) + 5)

    def __ifloordiv__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the ifloordiv //= operator

        >>> a = Sexagesimal('01, 12; 04, 17')
        >>> a //= Sexagesimal('7; 45, 55')
        >>> a
        09 ; 00,00

        :param other: other NumberView object
        :return: self
        """
        res = self // other
        self.set_from_other(res)
        return self

    def __itruediv__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the itruediv /= operator.

        TODO investigate the precision of this division berfore writing unitary tests

        :param other: the other NumberView object
        :return: self
        """
        res = self / other
        self.set_from_other(res)
        return self

    def __imod__(self, other: 'NumberView') -> 'NumberView':
        """
        Implementation of the imod %= operator.

        >>> a = Sexagesimal('01, 12; 04, 17')
        >>> a %= Sexagesimal('7; 45, 55')
        >>> a
        02 ; 11,02,00,00

        :param other: other NumberView object
        :return:
        """
        res = self % other
        self.set_from_other(res)
        return self

    def __g_helper(self, other: 'NumberView', saturation: bool = False) -> bool:
        """
        Helper function for the comparison of two NumberView objects.

        >>> Sexagesimal('23; 17')._NumberView__g_helper(Sexagesimal('11; 01, 22'))
        True
        >>> Sexagesimal('-23; 17')._NumberView__g_helper(Sexagesimal('11; 01, 22'))
        False
        >>> Sexagesimal('14; 17, 00')._NumberView__g_helper(Sexagesimal('14; 17'))
        False
        >>> Sexagesimal('14; 17, 00')._NumberView__g_helper(Sexagesimal('14; 17'), saturation=True)
        True

        :param other: The other NumberView object to compare with
        :param saturation: Choose the behaviour in case of equality
        :return: True if this NumberView object is greater than the other
        """
        if not isinstance(self, type(other)) or not isinstance(other, type(self)):
            raise TypeMismatch(
                'Conversion needed for operation between %s and %s' % (str(type(self)), str(type(other))))
        if self.sign < 0 and other.sign > 0:
            return False
        if self.sign > 0 and other.sign < 0:
            return True
        self.__simplify_integer_part()
        other.__simplify_integer_part()
        if len(self.leftList) != len(other.leftList):
            if len(self.leftList) > len(other.leftList):
                return True if self.sign > 0 else False
            else:
                return False if self.sign > 0 else True
        for i in range(-(len(self.leftList) - 1), min(len(self.rightList), len(other.rightList)) + 1):
            if self[i] > other[i]:
                return True if self.sign > 0 else False
            elif self[i] < other[i]:
                return False if self.sign > 0 else True
        if len(self.rightList) == len(other.rightList):
            if self.remainder > other.remainder:
                return True if self.sign > 0 else False
            if self.remainder < other.remainder:
                return False if self.sign > 0 else True
        if len(self.rightList) < len(other.rightList):
            small_len = len(self.rightList)
            small = copy.deepcopy(self)
            big = other
        else:
            small_len = len(other.rightList)
            small = copy.deepcopy(other)
            big = self
        small.resize(len(big.rightList))
        for i in range(small_len, len(big.rightList)):
            if big.rightList[i] > small.rightList[i]:
                if big is self:
                    return True if self.sign > 0 else False
                else:
                    return False if self.sign > 0 else True
            elif big.rightList[i] < small.rightList[i]:
                if big is self:
                    return False if self.sign > 0 else False
                else:
                    return True if self.sign > 0 else True
        return saturation

    def __gt__(self, other: 'NumberView') -> bool:
        """
        Implement the > test

        >>> Sexagesimal('01, 27; 00, 03') > Sexagesimal('01, 25; 00, 12')
        True
        >>> Sexagesimal('-01, 27; 00, 03') > Sexagesimal('01, 25; 00, 12')
        False

        :param other: other NumberView object
        :return: True if self is greater than other, False if not
        """
        return self.__g_helper(other, False)

    def __eq__(self, other: object) -> bool:
        """
        Tests equality with another NumberView object.

        >>> a = Sexagesimal('01, 21; 47, 25')
        >>> a == Sexagesimal('01, 21; 47, 25')
        True
        >>> a == Sexagesimal('01, 21; 47, 25, 00, 00')
        True
        >>> a == Sexagesimal('01, 21; 47, 25, 00, 01')
        False
        >>> from copy import deepcopy
        >>> b = deepcopy(a)
        >>> b.remainder = 0.15
        >>> b
        01,21 ; 47,25
        >>> a == b
        False
        >>> a == b.rounded()
        True

        :param other: other NumberView object
        :return: True if both NumberView objects are equal, False if not
        """
        if not isinstance(self, type(other)) or not isinstance(other, type(self)):
            raise TypeMismatch(
                'Conversion needed for operation between %s and %s' % (str(type(self)), str(type(other))))
        if self.sign != other.sign:
            return False
        self.__simplify_integer_part()
        other.__simplify_integer_part()
        if len(self.leftList) != len(other.leftList):
            return False
        for i in range(-(len(self.leftList) - 1), min(len(self.rightList), len(other.rightList)) + 1):
            if self[i] != other[i]:
                return False
        if len(self.rightList) == len(other.rightList):
            if self.remainder == other.remainder:
                return True
        if len(self.rightList) < len(other.rightList):
            small_len = len(self.rightList)
            small = copy.deepcopy(self)
            big = other
        else:
            small_len = len(other.rightList)
            small = copy.deepcopy(other)
            big = self
        small.resize(len(big.rightList))
        for i in range(small_len, len(big.rightList)):
            if big.rightList[i] != small.rightList[i]:
                return False
        if small.remainder != big.remainder:
            return False
        return True

    def __ne__(self, other: object) -> bool:
        """
        Implement the != operator

        >>> a = Sexagesimal('01, 21; 47, 25')
        >>> a != Sexagesimal('01, 21; 47, 25, 00')
        False
        >>> a.remainder = 0.19
        >>> a != Sexagesimal('01, 21; 47, 25, 00')
        True

        :param other: other NumberView object
        :return: True if self and other are different, False if not
        """
        return not self == other

    def __ge__(self, other: 'NumberView') -> bool:
        """
        Implement the >= operator

        >>> Sexagesimal('01, 25;') >= Sexagesimal('01, 25; 00, 00')
        True
        >>> Sexagesimal('01, 27; 00, 03') >= Sexagesimal('01, 25; 00, 12')
        True
        >>> Sexagesimal('-01, 27; 00, 03') >= Sexagesimal('01, 25; 00, 12')
        False

        :param other: other NumberView object
        :return: True if self is greater or equal to other, False if not
        """
        return self.__g_helper(other, True)

    def __lt__(self, other: 'NumberView') -> bool:
        """
        Implement the < test

        >>> Sexagesimal('01, 27; 00, 03') < Sexagesimal('01, 25; 00, 12')
        False
        >>> Sexagesimal('-01, 27; 00, 03') < Sexagesimal('01, 25; 00, 12')
        True

        :param other: other NumberView object
        :return: True if self is greater than other, False if not
        """
        return not self >= other

    def __le__(self, other: 'NumberView') -> bool:
        """
        Implement the <= operator

        >>> Sexagesimal('01, 25;') <= Sexagesimal('01, 25; 00, 00')
        True
        >>> Sexagesimal('01, 27; 00, 03') <= Sexagesimal('01, 25; 00, 12')
        False
        >>> Sexagesimal('-01, 27; 00, 03') <= Sexagesimal('01, 25; 00, 12')
        True

        :param other: other NumberView object
        :return: True if self is greater or equal to other, False if not
        """
        return not self > other


def print_inside(a: NumberView):
    """
    Function used to debug NumberView objects
    :param a:
    :return:
    """
    print(a.sign, a.leftList, a.rightList, a.remainder)


# here we define standard bases and automatically generate the corresponding NumberView classes
RadixBase([10], [10], 'decimal')
RadixBase([60], [60], 'sexagesimal')
RadixBase([60], [60], 'floating_sexagesimal')
RadixBase([30, 12, 10], [60], 'historical', ['s ', 'r ', ''])
RadixBase([10], [100], 'historical_decimal')
RadixBase([10], [60], 'integer_and_sexagesimal')
RadixBase([10], [24, 60], 'temporal')
# add new definitions here, corresponding NumberView inherited classes will be automatically generated


if __name__ == "__main__":
    print('====== Running doctests ======')

    import doctest
    doctest.testmod()

    print('==============================')
    print()

    print('== Running additional tests ==')

    import random

    def generate_numbers(base_name, n=10, max_integer_positions=5, max_fractional_positions=10):
        res = []
        base = RadixBase.name_to_base[base_name]
        for k in range(n):
            remainder = random.random()
            left = []
            right = []
            sign = 1 if random.random() < 0.5 else -1
            for i in range(random.randint(0, max_integer_positions)):
                left.append(random.randint(0, base.left[i] - 1))
            for i in range(random.randint(0, max_fractional_positions)):
                right.append(random.randint(0, base.right[i] - 1))
            res.append(base.type(left, right, remainder=remainder, sign=sign))
        return res

    def float_equality(a, b):
        # float are precise up to the 2 last digits of their representation
        # we compare the mantissa of the 2 floating numbers
        m_a, e_a = math.frexp(a)
        m_b, e_b = math.frexp(b)
        if e_a != e_b:
            return False
        if abs(m_a - m_b) > 0.0000000000005:
            return False
        return True

    def homogenize(a, b):
        if a.get_significant_places() < b.get_significant_places():
            a.resize(b.get_significant_places())
        if b.get_significant_places() < a.get_significant_places():
            b.resize(a.get_significant_places())

    def random_type():
        n_types = len(RadixBase.name_to_base)
        return list(RadixBase.name_to_base.values())[random.randint(0, n_types - 1)].type

    for name in RadixBase.name_to_base:
        current_type = RadixBase.name_to_base[name].type
        print()
        print('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~')
        print('** {} **'.format(name))
        print('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~')
        print()

        print('base->Fraction->base')
        for i in tqdm(range(1000)):
            a, = generate_numbers(name, n=1)
            if int(a) == 0:
                a.remainder = 0.0
            try:
                b = current_type.from_fraction(a.to_fraction(),
                                               remainder=a.remainder,
                                               significant=a.get_significant_places())
                assert float_equality(float(a), float(b))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("base->float->base")
        for i in tqdm(range(1000)):
            a, = generate_numbers(name, n=1)
            try:
                b = current_type(float(a), 10)
                assert float_equality(float(a), float(b))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("float->base->float")
        for i in tqdm(range(1000)):
            a, = generate_numbers(name, n=1)
            try:
                fa = float(a)
                b = current_type(fa, 10)
                assert float_equality(float(current_type(fa, 10)), float(current_type(float(b), 10)))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("base->base_2->base")
        for i in tqdm(range(1000)):
            a, = generate_numbers(name, n=1)
            try:
                b = current_type(random_type()(a, 15), 10)
                assert float_equality(float(a), float(b))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("__sanitize")
        for i in tqdm(range(1000)):
            a, = generate_numbers(name, n=1)
            try:
                for index in range(-len(a.leftList) + 1, len(a.rightList) + 1):
                    if random.random() < 0.5:
                        a[index] *= -1
                if random.random() < 0.5:
                    a.remainder *= -1.0
                a_clean = copy.deepcopy(a)
                a_clean._NumberView__sanitize()

                assert float_equality(float(a), float(a_clean))

            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("resized")
        for i in tqdm(range(1000)):
            a, = generate_numbers(name, n=1)
            try:
                a_resized = a.resized(random.randint(0, 10))

                assert float_equality(float(a), float(a_resized))
                a_resized.resize(a.get_significant_places())
                assert float_equality(float(a), float(a_resized))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("a + b")
        for i in tqdm(range(1000)):
            a, b = generate_numbers(name, n=2)
            try:
                assert float_equality(float(a) + float(b), float(a + b))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("a - b")
        for i in tqdm(range(1000)):
            a, b = generate_numbers(name, n=2)
            try:
                assert float_equality(float(a) - float(b), float(a - b))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("a + b - b == a")
        for i in tqdm(range(1000)):
            a, b = generate_numbers(name, n=2)
            try:
                assert float_equality(float(a + b - b), float(a))
                homogenize(a, b)
                assert (a + b - b).rounded() == a.rounded()
            except AssertionError:
                print()
                print(a + b - b)
                print(a)
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("a * b")
        for i in tqdm(range(1000)):
            a, b = generate_numbers(name, n=2)
            try:
                assert float_equality(float(a) * float(b), float(a * b))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("generalized euclidian division a = q x b + r")
        for i in tqdm(range(1000)):
            a, b = generate_numbers(name, n=2)
            try:
                q, r = a.euclidian_div(b)
                assert float_equality(float(a), float(q * b + r))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

        print("a.division(b, 10)")
        for i in tqdm(range(1000)):
            a, b = generate_numbers(name, n=2)
            try:
                res = a.division(b, 10)
                assert float_equality(float(a) / float(b), float(res))
            except AssertionError:
                traceback.print_exc()
                print()
                pdb.set_trace()
        print('***** OK')

    print('==============================')

