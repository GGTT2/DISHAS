
Tables
******

Entry point
===========

The main table can be created or reshaped with 2 functions:

.. js:autofunction:: createHotTable

.. js:autofunction:: respecHotTable

Inheritance scheme
==================

The graphical tables present in the DISHAS interface are implemented through several classes.

.. |br| raw:: html

	<br/>

.. |inheritance_image| image:: inheritance_scheme.png
	:width: 502pt
	:height: 424pt

.. centered::
	|inheritance_image| |br| |br|
	`Inheritance scheme for js tables`

.. js:autoattribute:: BoundingBoxes

Virtual classes
===============

First are defined virtual classes (i.e. interfaces) describing tables: information tables and the main table, without a choice for their graphical implementation.

.. js:autoclass:: TableInterface
	:members: containerId, verticalInformationTables, horizontalInformationTables, mathZones, diff1Warning, diff2Warning, areasToClean, lockSuper, createFromZone, render, fillCell, getValue, isActive, refocus, deselectCell, beforeKeyDown, getSelectedCells, selectCells, getSelectedCell, selectionToSuperCells, selectionToMetaCells, columnSelect, lineSelect, superSelect, isSelectionSuperSelect, lockSuper, moveUp, moveRight, moveDown, moveLeft, moveSelectionsHorizontally, moveSelectionsVertically, moveSelectionsNextSuperCell, nop, *

.. js:autoclass:: InfoTableInterface
	:members: vertical, graphicZone, selectedMetaZone, mathZone, tool, highlights, readonly, updatedOnce, createFromZones, updateGraphicZone, *

.. js:autoclass:: MainTableInterface
	:members: zones, selectedMetaZone, model, graphType, keyBindings, keyTimes, toolsKeyBindings, oldArgPos, parameter_snfs, maxQueue, snapshotQueue, redoQueue, activateTool, previewTool, cleanPreview, previewOrigin, cleanPreviewOrigin, addZone, switchZone, getSelectedSuperCell, plusMinusOne, plusOne, minusOne, validateSelection, validateAll, touchAllValidated, redo, undo, hasChanged, fillStarsIfEmpty, beforeKeyDown, keyDown, keyUp, onSelectionChanged, typeChanged, performLSQ, fillTableFromModel, fillGraph, createMathZone, createVerticalComputation, createHorizontalComputation, createLinkedComputation, removeVerticalInformationTable, removeHorizontalInformationTable, updateInfos, renderInfos, highlightInfos, getVerticalInfoId, getHorizontalInfoId, resizeVerticalInfoDiv, resizeHorizontalInfoDiv, createVerticalInfoHeader, createHorizontalInfoHeader, removeVerticalInfoDiv, removeHorizontalInfoDiv, createMathZone, createVerticalComputation, createHorizontalComputation, createLinkedComputation, removeVerticalInformationTable, removeHorizontalInformationTable, onSwitchZone, onNewModel, focusCommentary, focusLog, showCommentary, showGraph, updateLog, updateMathematicalParameters, interfaceBindings, updateSettings, snapshot, restore, *

HTML Information tables
=======================

We decide to implement information tables with simple HTML tables, since there is no need for user interaction with this kind of tables.

.. js:autoclass:: InfoHTMLTable
	:members: containerId, data, R, C, createFromZone, iterateDomCells, destroy, render, *

Handsontable Main table
=======================

The main table is implemented thanks to the `HandsOnTable`_ library.

.. _HandsOnTable: https://handsontable.com/

.. js:autofunction:: cellFunctionHot
.. js:autofunction:: cellRenderer

.. js:autoclass:: HotTable
	:members: hot, selection, createFromZone, getSelectedCells, selectCell, selectCells, deselectCell, isActive, afterChange, beforeKeyDown, dataSelected, postRender, render, destroy, synchronizeHorizontalScrollings, synchronizeVerticalScrollings, *

.. js:autoclass:: DTITable
	:members: interfaceBindings, updateAstronomicalParameters, updateMathematicalParameters, transferParameters, parametersToFields, showGraph, focusCommentary, focusLog, showCommentary, updateLog, onNewModel, onSwitchZone, getVerticalInfoId, getHorizontalInfoId, createVerticalInfoDiv, createHorizontalInfoDiv, resizeVerticalInfoDiv, resizeHorizontalInfoDiv, createVerticalInfoHeader, createHorizontalInfoHeader, removeVerticalInfoDiv, removeHorizontalInfoDiv, removeVerticalInformationTable, removeHorizontalInformationTable, buttonHorizontalString, buttonVerticalString, updateSettings, synchronizeHorizontalScrollings, synchronizeVerticalScrollings, *
