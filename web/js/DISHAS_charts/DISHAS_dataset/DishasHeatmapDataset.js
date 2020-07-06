/* jshint esversion: 6 */

/**
 * This method defines a dataset that can be used to generate a DishasXYChart
 * in order to create a timeline and to fill a instance of DishasDataset
 */
class DishasHeatmapDataset {
    constructor(){
        this.type = "Heatmap";
        this.data = [];
    }

    /**
     * This method adds all instances of DishasTimelineData
     * between two dates for each series mentioned in the series array
     * It can be used to generate
     *
     * @param minDate int : min date
     * @param maxDate int : max date
     * @param series array : names of all series that will appear in the timeline
     */
    addAllData(minDate, maxDate, series){
        for (minDate; minDate <= maxDate; minDate += 10) {
            if (typeof this.data[minDate] === 'undefined'){
                this.data[minDate] = new DishasTimelineData(minDate, series);
            }
        }

        // Remove empty indexes
        this.data = this.data.filter(Boolean);
    }

    /**
     * This method adds an instance of DishasTimelineData (new date) to the dataset or
     * adds an item to the count of the series
     *
     * @param date int : current year
     * @param series array : names of all series that will appear in the timeline
     * @param seriesName string : name of one of the series in order to add one to its count
     * @param id int : id of the record to add to the count
     */
    addData(date, series, seriesName, id){
        let entityInfo = new DishasEntity();

        if (typeof this.data[date] === 'undefined'){
            // if there is no data for this date already
            // create a data object
            this.data[date].push(new DishasHeatmapData(date, series, seriesName, `${entityInfo[seriesName].prefixId}${id}`));
        } else {
            this.data[date][seriesName] += 1;
            this.data[date].ids.push(`${entityInfo[seriesName].prefixId}${id}`);
        }
    }

    /**
     * This function generates an object detailing for each year how many work and primary sources
     * were created in order to create a timeline for
     *
     * The return array looks like:
     * [
     *     {
     *       "year": 1000,
     *       "work": 2,
     *       "primarySource": 1,
     *       "ids": ["ps1", "w4", etc.],
     *       "i": "i" // in order to show a heatmap = contains no real information
     *     }
     * ]
     * @param works : array of objects given as results of an elasticsearch query
     * @param originalTexts : array of objects given as results of an elasticsearch query
     */
    static getTimeline(works, originalTexts) {
        let data = {};
        let i;

        if (works.length > 0){
            for (i = works.length - 1; i >= 0; i--) {
                // round up the date to the decade
                let year = isDefined(works[i].tpq) ? roundUpNumber(works[i].tpq, 0) : 0;
                let taq = isDefined(works[i].taq) ? roundUpNumber(works[i].taq, 0) : 0;

                for (year; year <= taq; year += 10) {
                    if (typeof data[year] !== 'undefined'){
                        data[year].work += 1;
                        data[year].ids.push(`wo${works[i].id}`);
                    } else {
                        data[year] = {
                            "year": year,
                            "i": "i",
                            "work": 1,
                            "primarySource": 0,
                            "ids": [`wo${works[i].id}`]
                        };
                    }
                }
            }
        }

        if (originalTexts.length > 0){
            for (i = originalTexts.length - 1; i >= 0; i--){
                // round up the date to the decade
                let year = isDefined(originalTexts[i].tpq) ? roundUpNumber(originalTexts[i].tpq, 0) : 0;
                let taq = isDefined(originalTexts[i].taq) ? roundUpNumber(originalTexts[i].taq, 0) : 0;

                for (year; year <= taq; year += 10) {
                    if (typeof data[year] !== 'undefined'){
                        data[year].primarySource += 1;
                        // if the current id is already in the array of ids
                        if (typeof originalTexts[i].primary_source !== "undefined"){
                            if (! (data[year].ids.indexOf(`ps${originalTexts[i].primary_source.id}`) > -1)){
                                data[year].ids.push(`ps${originalTexts[i].primary_source.id}`);
                            }
                        }
                    } else {
                        if (typeof originalTexts[i].primary_source !== "undefined"){
                            data[year] = {
                                "year": year,
                                "i": "i",
                                "work": 0,
                                "primarySource": 1,
                                "ids": [`ps${originalTexts[i].primary_source.id}`]
                            };
                        }
                    }
                }
            }
        }

        if (Object.keys(data).length !== 0){
            // in order to show decades where nothing happened
            let minDate = parseInt(Object.keys(data)[0])-20;
            let maxDate = parseInt(Object.keys(data)[Object.keys(data).length-1])+20;

            for (minDate; minDate <= maxDate; minDate += 10) {
                if (typeof data[minDate] === 'undefined'){
                    data[minDate] = {"year": minDate, "i": "i", "work": 0, "primarySource": 0};
                }
            }
        }

        return data/*sortArrayOfObjectsByKey(objectToArray(data), "year")*/;
    }
}

/**
 * This class defines the object that can be used to fill an instance of DishasMapDataSet.
 *
 * Example:
 * {
 *       "year": 1000,
 *       "work": 2,
 *       "primarySource": 1,
 *       "ids": ["ps1", "w4", etc.],
 *       "i": "i" // in order to show a timeline = contains no real information
 * }
 *
 */
class DishasHeatmapData {
    /**
     * @param year int : current year
     * @param series array : names of all series that will appear in the timeline
     * @param seriesName string : name of one of the series in order to add one to its count
     * @param id int : id of the record to add to the count
     */
    constructor(year, series, seriesName="", id=0){
        this.year = year;
        this.i = "i";
        // add the id the array of ids if there is one
        this.ids = id === 0 ? [] : [id];

        // create a property per series name in the "series" array
        for (var i = series.length - 1; i >= 0; i--) {
            // if the series name given as parameter is the same as the current property
            // add one to the count of this series
            this[series[i]] = seriesName === series[i] ? 1 : 0;
        }
    }
}
