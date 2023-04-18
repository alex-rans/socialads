const $ = require('jquery');

import * as bootstrap from 'bootstrap'
import '../styles/main.scss';
// import './sortable'
import './calculator';


$(document).ready(function (){
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
})

// delete post modal
$('.deleteProduct').click(function () {
    const id = $(this).data("id");
    const name = $(this).data("name");
    let msg = `Delete the post ${name}?`;
    $('#deleteModalText').text(msg)
    $('#deleteModal').show();
    $('[data-bs-dismiss=\'modal\']').click(function (){
        $('#deleteModal').hide();
    })
    $('#deleteModalConfirm').click(function (){
        document.location.href = `/admin/post/${id}/delete`
    })
})

$('.newList').click(function () {
    $('#deleteModal').show();
    $('[data-bs-dismiss=\'modal\']').click(function (){
        $('#deleteModal').hide();
    })
    $('#deleteModalConfirm').click(function (){
        document.location.href = `/`
    })
})

// remove alert button
$('.btn-close').click(function (){
    $(this).parent().remove();
})