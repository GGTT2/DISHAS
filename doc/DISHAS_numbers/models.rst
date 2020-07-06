
Models
******

In order to ease the input of a table in the interface, we provide the possibility to pre-fill a table thanks to the values computed from a modern astronomical formula.

Parmeter values can be estimated thanks to a Least Square procedure, provided the user entered some values of the table.

.. js:autoclass:: Parameter
	:members: value, default_value, locked, linkedParameter, transform, bounds, *

.. js:autoclass:: ParametricFunction
	:members: name, description, parameters, dimension, jsFunction, jsDerivatives, tool, shifted, argumentShifted, parameterValues, setParameterValue, setParameterValues, toolHelper, apply, apply_derivate, valueListToValues, valuesToValueList, approximate_derivate, *

.. js:autofunction:: loadModelJSON

.. js:autofunction:: LSQ
