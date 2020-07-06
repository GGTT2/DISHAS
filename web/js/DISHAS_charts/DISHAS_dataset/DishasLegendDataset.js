/* jshint esversion: 6 */

/* ==================================================== LEGEND DATA ==================================================== */
/**
 * This method defines a dataset that can be used to generate a DishasLegend
 * and to fill a instance of DishasDataset
 *
 * It allows the legend to make the legend items that correspond to an astronomical object
 * that is represented by the original items present in the chart clickable
 *
 * DishasLegendDataset = {astroObjectId1: [ttId1, oiId1, oiId2], astroObjectId2: [ttId2, oiId3, oiId4, oiId5]};
 */
class DishasLegendDataset {
    constructor(){
        this.type = "Legend";
        this.data = {};
    }

    /**
     *
     * @param astroId int : id of the astronomical object
     * @param idToAdd string : prefixed id of the original item that is associated with the astronomical object
     */
    addData(astroId, idToAdd){
        if (this.data.hasOwnProperty(astroId)){
            this.data[astroId].push(idToAdd);
        } else {
            this.data[astroId] = [`ao${astroId}`, idToAdd];
        }
    }
}