
Conversion module
*****************

.. |br| raw:: html

	<br/>

This module allows the manipulation of historical values. All the numeral systems supported in DISHAS are positional (see `positional notation <https://en.wikipedia.org/wiki/Positional_notation>`_), but some have the particularity to have a `radix <https://en.wikipedia.org/wiki/Radix>`_ depending on the position (`mixed radix <https://en.wikipedia.org/wiki/Mixed_radix>`_).
Thus we represent a numeral system (which we will call \ :js:attr:`TypeOfNumber`\  from now on) as:

- An infinite sequence of radix for the integer positions
- An infinite sequence of radix for the fractionnal positions
- A name

.. admonition:: Example
	:class: highlight

	As an example, we describe the *historical* numeral system.
	This system uses a radix of 60 for every position of its fractionnal part (similar to *sexagesimal*). Its integer part, however, is decomposed into *signs* and *revolutions*. Each *revolution* contains 12 *signs*, and each *sign* contains 30 units. Thus, the decimal number :math:`41009.18` would be written in this numeral system as :math:`113\mathrm{r}\enskip 10\mathrm{s}\enskip 29\enskip \boldsymbol; 10, 48`.

	This numeral system can be represented as 2 radix sequences: :math:`[30, 12, 10, 10, ...]` for the integer part, and :math:`[60, 60, 60, ...]` for the fractional part.

	.. math::
	
		\underset{10}{1}\enskip \underset{10}{1}\enskip \underset{10}{3}\mathrm{r}\enskip \underset{12}{10}\mathrm{s}\enskip \underset{30}{29}\enskip \boldsymbol; \underset{60}{10}, \underset{60}{48}

To represent a quantity in a specific \ :js:attr:`TypeOfNumber`\ , we store it as the list of its values for every position.
More specifically, we store the list of its integer positionnal values, and a list of its fractionnal positionnal values.


.. admonition:: Example
	:class: highlight

	The sexagesimal number :math:`01, 09, 17 ; 24, 35, 59` would be stored as 2 list:

	- :math:`l=[17, 9, 1]` for the integer part
	- :math:`r=[24, 35, 59]` for the fractional part

	.. math::
	
		\underset{l_2}{01}, \underset{l_1}{09}, \underset{l_0}{17} \boldsymbol; \underset{r_0}{24}, \underset{r_1}{35}, \underset{r_2}{59}

.. note:: The integer positions of a quantity are indexed backward: this important for implementation reasons.

.. js:autoattribute:: TypeOfNumber
.. js:autoattribute:: RadixBase

.. js:autoattribute:: nameToBase


Number Views
============

.. js:autoclass:: NView
	:members: leftList, rightList, remainder, sign, base, copy, add, equals, toString, toFloat64, toBase, toInt, fromString, float64ToBase, intToBase, resize, truncate, round, *, removeTrailingZeros, simplifyIntegerPart, naiveSanitize, fractionnal_position_base_to_base

Smart Numbers
=============

.. |multi| raw:: html
	
	<ul style="display: flex; list-style: none;"><li>

.. |_multi| raw:: html
	
	</li></ul>

.. |or| raw:: html
	
	</li><li style="position:absolute; left: 620px;"><b>|</b>&nbsp;&nbsp;

.. js:autoclass:: SmartNumber
	:members: decimal, tobases, initialbase, computeDecimal, computeBase, *
