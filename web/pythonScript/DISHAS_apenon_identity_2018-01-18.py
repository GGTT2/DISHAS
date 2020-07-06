
from smartnumber import SmartNumberFromJson


def customScript(args):
    return SmartNumberFromJson(args).to_json()
