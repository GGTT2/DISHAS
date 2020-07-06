

from smartnumber import SmartNumberFromJson
from smartnumber import SmartNumber
import sys
from sys import stderr


def customScript(args):
    # Mapping JSON -> donnees
    stderr.write(str(args) + '\n')
    secondEntry = SmartNumberFromJson(args[1][0])
    firstEntry = SmartNumberFromJson(args[1][1])

    firstArgument = SmartNumberFromJson(args[0][1])
    secondArgument = SmartNumberFromJson(args[0][0])

    lastArgument = SmartNumberFromJson(args[2][0])

    stderr.write(str(firstEntry) + '\n')
    stderr.write(str(secondEntry) + '\n')

    stderr.write(str(firstArgument) + '\n')
    stderr.write(str(secondArgument) + '\n')

    # Algorithm
    slope = ((secondEntry.compute_decimal() - firstEntry.compute_decimal()) /
             (secondArgument.compute_decimal() - firstArgument.compute_decimal())
             )

    value = (secondEntry.compute_decimal() + (lastArgument.compute_decimal() -
             secondArgument.compute_decimal()) * slope)

    res = SmartNumber(value)

    # Mapping result -> JSON
    return res.as_base(
        firstEntry.initialbase,
        firstEntry.get_significant()
    ).round().to_json()
