<?php
namespace TAMAS\AstroBundle\DISHASToolbox;

Class VisualisationDefinitions
{
    static function getDefinition($visualisationName) {
        $definitions = [
            "chronoMap-OI-rec" =>
                '<div class="dataviz-def">
                    <p>This visualisation displays the context of creation of this original item and the 
                    work from which it derives.</p>
                    <p>The map shows the place of copy of the part of the primary source containing the original item (red),
                    and the place of conception of the work (yellow).
                    The heat map represents the timeline of creation of this original item and this work: 
                    the bars indicate the periods where they have been created. 
                    If the date of creation of an item is not precisely known, the bar can cover a long period of time.</p>
                    <p>The scrollbar above the heat map can be used to select a time range, 
                    in order to show only the items created during it.</p>
                    <p>It is possible to click on the map points or heat map strips to show the records of the work and 
                    the primary source associated with this item that were created at this place/decade.</p>
                </div>',

            "chronoMap-hist-nav" =>
                '<div class="dataviz-def">
                    <p>This visualisation displays all the works and the primary sources of the database.</p>
                    <p>The map shows the places of conception of the works (yellow), and the places of copy of the primary sources (red).
                    The different parts (original items) of a primary source may have been created in multiple places: 
                    the same primary source may therefore be represented several times on the map.
                    The heat map represent the timeline of creation of those works and primary sources: 
                    the color variations indicate the fluctuation in terms of intellectual blossoming through time. 
                    The more saturated, the more items have been created at this period.</p>
                    <p>The scrollbar above the heat map can be used to select a time range, 
                    in order to show only the items created during it.</p>
                    <p>It is possible to click on the map points or heat map strips to show the works and primary sources 
                    that were created at this place/decade.</p>
                    <p>The visualization allows to identify time periods and geographical areas of intellectual blossoming.</p>
                </div>',

            "chronoMap-AP-rec" =>
                '<div class="dataviz-def">
                    <p>This visualisation displays all the original items that use this parameter set in the database.</p>
                    <p>The map shows the places of conception of those original items.
                    The heat map represent the timeline of creation of those original items: 
                    the color variations indicate the fluctuation in terms of intellectual blossoming through time. 
                    The more saturated, the more items have been created at this period.</p>
                    <p>The scrollbar above the heat map can be used to select a time range, 
                    in order to show only the items created during it.</p>
                    <p>It is possible to click on the map points or heat map strips to show the original items 
                    that were created at this place/decade.</p>
                    <p>The visualization allows to depicts the circulation of astronomical parameters through time and space.</p>
                </div>',

            "chronoMap-TE-rec" =>
                '<div class="dataviz-def">
                    <p>This visualisation displays all the primary sources and the work on which this edition is based upon.</p>
                    <p>The map shows the places of conception of those sources.
                    The heat map represent the timeline of creation of those sources: 
                    the color variations indicate the fluctuation in terms of intellectual blossoming through time. 
                    The more saturated, the more items have been created at this period.</p>
                    <p>The scrollbar above the heat map can be used to select a time range, 
                    in order to show only the items created during it.</p>
                    <p>It is possible to click on the map points or heat map strips to show the original items 
                    that were created at this place/decade.</p>
                    <p>The visualization allows to show the diffusion of a particular table through time and space.</p>
                </div>',

            "barChart" =>
                '<div class="dataviz-def">
                    <p>This visualisation represents the works that are contained in the primary source from the present record. 
                    Each bar corresponds to a work; each element in this bar represents an original item derived from this work 
                    that is included in this primary source.</p>
                    <p>The visualisation can takes two forms; the user can switch between them by selecting an option 
                    in the dropdown button above the chart. The "<i>Page disposition</i>" option presents the original items 
                    as they are arranged in the source, whereas the "<i>Page quantity</i>" option displays them side by side with each other 
                    in order to compare the quantity of items from each work. Some original items might be localised on the same 
                    pages, those are represented stacked on top of each other in the "<i>Page disposition</i>" option.</p>
                    <p>The width of the bar elements is proportional to the number of pages occupied by the original items in the source. 
                    The color refers to the astronomical object on which the original item is focused.</p>
                    <p>This chart allows to visualize the intricacy of the different parts of a primary source, 
                    while having an indication of the content of the original items that compose it.</p>
                </div>',

            "columnChart" =>
                '<div class="dataviz-def">
                    <p>This visualisation represents all the primary sources which contain a part of the work from the present record. 
                    Each column corresponds to a primary source; each element in this column represents an original item 
                    contained in the source that is derived from this work.</p>
                    <p>The presented primary source might contain other original items that are not originated from the present work;
                    they are not represented on this chart.
                    The height of the column elements is proportional to the number of pages occupied by the original items in the source. 
                    The color refers to the astronomical object on which the original item is focused.</p>
                    <p>It is possible to click on column labels and and column element to access the records of the primary sources 
                    and original items represented.</p>
                    <p>This chart allows to visualize the completeness of the present work 
                    in the various primary sources recorded in the database, 
                    while having an indication of the content of the original items that compose them.</p>
                </div>',

            "treemap-astro" =>
                '<div class="dataviz-def">
                    <p>This visualisation represents all the astronomical objects of the database and the table types associated with them.</p>
                    <p>Each cell of the treemap represents one astronomical object, its size is determined by the number
                    of editions and astronomical parameter that are related to this object</p>
                    <p>By clicking on one of these cells, a definition and the table types associated with the object will appear.</p>
                    <p>This chart allows to visualize what types of tables are more commonly used in the database.</p>
                </div>',

            "treemap-param" =>
                '<div class="dataviz-def">
                    <p>This visualisation represents all the parameter sets that are used at least once in a table edition.</p>
                    <p>Each parameter set is symbolized by a box whose size is determined by the number of table editions using it.</p>
                    <p>Each color represents a category of parameters, often associated with an astronomical object.</p>
                    <p>This chart allows to visualize what parameter sets are more commonly used in the table of the database.</p>
                </div>',

            "graph" =>
                '<div class="dataviz-def">
                    <p>This visualisation represents all the original items and table editions on which the current edition is based upon.</p>
                    <p>Each blue node represents an edition, each orange node represents an original item:
                    you can access their record page by clicking on them.</p>
                    <p>This graph allows to visualize the sources that were used in a edition.</p>
                </div>',

            "pieChart" =>
                '<div class="dataviz-def">
                    <p>This visualisation represents the proportion of each astronomical object:
                    each portion of the pie chart is linked to the number of parameter sets and table editions
                    associated to a particular astronomical object in the database.</p>
                    <p>By clicking on one of these portions, it is possible to show those numbers
                    divided by table type.</p>
                </div>'
        ];

        return $definitions[$visualisationName];
    }
}