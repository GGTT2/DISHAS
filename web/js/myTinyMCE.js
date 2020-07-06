//web\js\myTinyMCE.js

//my specification of TinyMCE
tinymce.init({
    selector: '.tinymce',
    theme: 'modern',
     height: 300,
    plugins: [
        'advlist autolink lists link charmap print preview hr anchor pagebreak ',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc latex'
    ],
    toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | preview ',
    toolbar2: 'print preview media | forecolor backcolor | codesample | latex',
    image_advtab: true,
    templates: [
        {title: 'Test template 1', content: 'Test 1'},
        {title: 'Test template 2', content: 'Test 2'}
    ],
    content_css: [
        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
        '//www.tinymce.com/css/codepen.min.css'
    ]
});
        
