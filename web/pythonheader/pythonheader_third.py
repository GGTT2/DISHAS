if len(sys.argv) != 2:
    raise ValueError("Incorrect number of arguments!!")
else:
    obj = json.loads(sys.argv[1])
    res = operation(obj)
    print(json.dumps(res))

