/* global $ */
/* global progressbarOpened */
/* global progressbarChunk */
/* global progressbarLength */

/**
 * Javascript is a gracious and powerful language in which a "long" loop of computation will freeze the UI until it is finished.
 * Every call to a graphic object inside the loop will be delayed until the end, preventing the possibility to display a progressbar.
 * After several second, a message will appear in the window stating that a page is slowing down the user's browser, who will then
 * proceed to close the tab.
 *
 * To avoid this, one must cut down a for loop into several chunks, which will be executed asynchronously on their own call stack thanks
 * to the setTimeout function. They are launched sequentially with a small delay between them, so that they execute (roughly) in order.
 * Once a chunk is finished, it can update the progressbar.
 * 
 * @param  {Number}     length          The number of ierations in the loop
 * @param  {Number}     index           The current index in the for loop
 * @param  {Number}     lengthOfChunk   The number of iterations performed before updating the progressbar
 * @param  {function}   operationFx     The operation to perform each iteration
 * @param  {function}   endOperation    The operation to perform at the end of the loop
 * @param  {Number}     maxlength       The length needed for the loop to display a progress bar
 * @param  {Number}     [numero=0]      Index of the progress bar (useful in case of nested loops)
 * @return {undefined}
 */
function forWrapper(length, index, lengthOfChunk, operationFx, endOperation, maxlength, numero) {
    if(numero === undefined) {
        var numero = 0;
    }
    if(maxlength === undefined) {
        var maxlength = lengthOfChunk;
    }
    progressbarChunk[numero] = lengthOfChunk;
    if(length >= maxlength) {
        initProgressBar(length,lengthOfChunk,numero);
        setTimeout(function() {
            chunkIteration(length, index, lengthOfChunk,
                function(i) {
                    stepProgressBar(i, numero);
                    operationFx(i);
                }, function() {
                    closeProgressBar(numero);
                    endOperation();
                }, numero
            );
        }, 50);
    }
    else {
        for(var i=0; i<length; i++) {
            operationFx(i);
        }
        endOperation();
    }
}

/**
 * Helper function for the forWrapper. It will execute one chunk of the loop.
 * 
 * @param  {Number}     length          The number of ierations in the loop
 * @param  {Number}     index           The current index in the for loop
 * @param  {Number}     lengthOfChunk   The number of iterations performed before updating the progressbar
 * @param  {function}   operationFx     The operation to perform each iteration
 * @param  {function}   endOperation    The operation to perform at the end of the loop
 * @return {undefined}
 */
function chunkIteration(length, index, lengthOfChunk, operationFx, endOperation) {
    var j, l = length;
    for (j = index + lengthOfChunk; index < j && index < l; index += 1) {
        operationFx(index);
    }
    if (l > index) {
        setTimeout(function() {
            chunkIteration(length, index, lengthOfChunk, operationFx, endOperation);
        }, 10);
    }
    else {
        endOperation();
    }
}

/*
 * The following bloc defines functions to manipulate a graphic progressbar.
 */

// a global variable stating if a progressbar has been opened or not
progressbarOpened = false;

// memory arrays holding the parameters for the 3 levels of progress bar
progressbarLength = [0, 0, 0];
progressbarChunk = [0, 0, 0];

/**
 * Function to open a progressbar.
 * 
 * @param  {Number} length      The number of ierations in the loop
 * @param  {Number} maxLength  Length needed for the loop to display a progress bar
 * @param  {Number} [numero=0]  Index of the progress bar (useful in case of nested loops)
 * @return {undefined}
 */
function initProgressBar(length, maxLength, numero) {
    if(numero === undefined)
        var numero = 0;
    if(length > maxLength) {
        $("#progressbar"+String(numero)).progressbar({value: 0});
        $("#progress-dialog").dialog("open");
        progressbarOpened = true;
        progressbarLength[numero] = length;
    }
}

/**
 * Function to update a progress bar.
 * 
 * @param  {Number} counter     The current index in the for loop
 * @param  {Number} [numero=0]  The index of the progress bar (useful in case of nested loops)
 * @return {undefined}
 */
function stepProgressBar(counter, numero) {
    if(numero === undefined)
        var numero = 0;
    if(progressbarOpened) {
        if((counter)%progressbarChunk[numero] === 0) {
            $("#progressbar"+String(numero)).progressbar({value: ((counter+1)*100)/progressbarLength[numero]});
        }
    }
}

/**
 * Function to close a progressbar.
 * 
 * @param  {Number} [numero=0]
 * @return {undefined}
 */
function closeProgressBar(numero) {
    if(numero === undefined)
        var numero = 0;
    
    progressbarLength[numero] = 0;
    progressbarChunk[numero] = 0;
    
    if(progressbarLength[0] === 0 && progressbarLength[1] === 0 && progressbarLength[2] === 0) {
        $("#progress-dialog").dialog("close");
        progressbarOpened = false;
    }
    
}
