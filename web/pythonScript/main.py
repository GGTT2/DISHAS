#!/usr/bin/python3

import sys
import json
import importlib


if len(sys.argv) != 3:
    raise ValueError("Incorrect number of arguments!!")

custom = importlib.import_module(sys.argv[1][:-3])

arguments = json.loads(sys.argv[2])

result = custom.customScript(arguments)

print(json.dumps(result))
