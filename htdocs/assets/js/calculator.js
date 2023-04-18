// import "./sortable";

const math = require("lodash");
const bootstrap = require("bootstrap");
const {append} = require("vary");
import './jquery-sortable';

let total = 0;
let totalTime = 0;
let totalPrice = 0;
let PMval = 20;
let postList = [];
let csv = 'Name,Type,Base cost,Rate,Extra languages,Extra channels,Total\n';
let title = ''
let mediaPosts
let decimals = false

function Post(id, type, rate, cost, languages, channels, langcost, chancost, name, childPosts) {
    this.id = id;
    this.type = type;
    this.rate = rate;
    this.cost = cost;
    this.languages = languages;
    this.channels = channels;
    this.langcost = langcost;
    this.chancost = chancost;
    this.name = name;
    this.childPosts = childPosts;
}
$( ".sortable-list" ).sortable({
    appendTo: document.body,
    placeholder: "sortable-placeholder"
});

//read slider at pageload idk why but its inverted now this is stupid
if ($("input[type='checkbox']").is(':checked')) {
    decimals = false
} else {
    decimals = true
}
updateTotal();


//get array of posts
$.ajax({
    url: '/get/posts',
    type: 'GET',
    success: function (data){
        mediaPosts = data['posts']
    }
})

//for loading post
const url = new URL(window.location.href);
const hash = url.searchParams.get('key');
if (url.searchParams.has('key')) {
    $.ajax({
        url: '/get/'+hash,
        type: 'GET',
        success: function (data){
            data["data"].forEach(addData)
            changeName(data['name'])
            $('#nameinput').val(data['name'])
            console.log(data)
            if(data['name'].length !== 0){
                document.title = data['name']
            }
        }
    })
}
function addData(item){
    total += 1
    let post = new Post(total, item['type'], item['rate'], item['cost'], 0, 0,
        item['langcost'], item['chancost'], item['name'], [])
    postList.push(post);
    addPost(post, total);
    for(let x = 0; x !== item['languages']; x++){
        addLang(post);
    }
    for(let x = 0; x !== item['channels']; x++){
        addChan(post);
    }
    item['childPosts'].forEach(function (item){
        const childPostId = item['id']
        const childPost = mediaPosts.find(x => x['id'] == childPostId)
        addChildPost(post, childPost)
    })
    updateTotal();
    loadTooltips();
}

//for adding post
$('#addPost').click(function () {
    total += 1
    const selected = $('#postSelect option:selected');
    const type = selected.text();
    const cost = parseFloat(selected.val());
    const rate = selected.data('rate');
    const extralang = selected.data('langcost');
    const extrachan = selected.data('chancost');
    let post = new Post(total, type, rate, cost, 0, 0, extralang, extrachan, `Post ${total}`, []);
    postList.push(post);
    addPost(post, total);
    loadTooltips();
    $( ".sortable-list" ).sortable( "option", "connectWith", ".childItems" );

})

//delete post click
$('.list').on("click", ".deletePost", function () {
    const element = $(this).parent().parent();
    const objId = $(this).parent().parent().parent().parent().attr('id');
    let post = postList.findIndex(x => x['id'] == objId);
    deletePost(post, element);
    bootstrap.Tooltip.getInstance(this).dispose()
})

//add lang click
$('.list').on("click", ".addLangButton", function () {
    const objId = $(this).parent().parent().parent().parent().attr('id')
    let post = postList.find(x => x['id'] == objId);
    addLang(post);
    bootstrap.Tooltip.getInstance(this).hide();
    loadTooltips();
})

//delete lang click
$('.list').on("click", ".deleteLang", function () {
    const element = $(this).parent().parent();
    const objId = $(this).parent().parent().parent().parent().attr('id');
    let post = postList.find(x => x['id'] == objId);
    deleteLang(post, element);
    bootstrap.Tooltip.getInstance(this).dispose()
    loadTooltips();
})

//add channel click
$('.list').on("click", ".addChanButton", function () {
    const objId = $(this).parent().parent().parent().parent().attr('id');
    let post = postList.find(x => x['id'] == objId);
    addChan(post);
    bootstrap.Tooltip.getInstance(this).hide();
    loadTooltips();
})

//delete channel click
$('.list').on("click", ".deleteChan", function () {
    const element = $(this).parent().parent();
    const objId = $(this).parent().parent().parent().parent().attr('id');
    let post = postList.find(x => x['id'] == objId);
    deleteChan(post, element);
    bootstrap.Tooltip.getInstance(this).dispose();
    loadTooltips();
})

//add child post
$('.list').on('click', '.addChildPostButton', function (){
    const id = $(this).parent().parent().parent().parent().attr('id');
    const childPostId = $(`select[data-option-id="${id}"] option:selected`).val()
    let post = postList.find(x => x['id'] == id);
    let childPost = mediaPosts.find(x => x['id'] == childPostId);
    addChildPost(post, childPost);
    bootstrap.Tooltip.getInstance(this).hide();
    loadTooltips();

})

//delete child post
$('.list').on('click', '.deleteChildPost', function (){
    const element = $(this).parent().parent();
    const objId = $(this).parent().parent().parent().attr('id');
    const childId = $(this).data('id')
    let post = postList.find(x => x['id'] == objId);
    let childpost = post['childPosts'].findIndex(x => x['id'] == childId);
    deleteChildPost(post, element, childpost);
    bootstrap.Tooltip.getInstance(this).hide();
    loadTooltips();
})

//export csv button
$('.resultdiv').on('click', '#exportcsv', function () {
    const PM = totalTime * (PMval / 100)
    csv = 'Name,Type,Base cost,Rate,Extra languages,Extra channels,Extra child posts,Total\n';
    postList.forEach(writeCsvLine);
    csv += ',,,,,,,\n';
    csv += `Content strategy,PM,Total uren, Total price,,,,\n`;
    csv += `${decimalToTime(0.25)},${PMval}%,${decimalToTime(totalTime + 0.25 + PM)},€${totalPrice},,,`;
    const date = new Date().toLocaleString()
    const blob = new Blob([csv], {type: 'text/csv;charset=utf-8,'})
    const objUrl = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.setAttribute('href', objUrl)
    link.setAttribute('download', `Export ${date}`)
    link.click();
})

//change PM
$('.totalresult').on("change", "#pminput", function () {
    PMval = $(this).val()
    if (PMval > 100) {
        PMval = 100
    } else if (PMval < 0) {
        PMval = 0
    }
    updateTotal();
})

//change post name
$('.list').on("change", ".nameinput", function (){
    const name = $(this).val();
    const id = $(this).parent().parent().parent().parent().attr('id');
    let post = postList.find(x => x['id'] == id);
    post['name'] = name;
    updateTotal();
})

//change title name
$('#nameinput').change(function (){
    title = $(this).val();
    document.title = title
    changeName(title);

})

// decimal slider
$('.slider').click(function(){
    //idk why but it only triggers when unchecked but whatever i can work with this
    if($("input[type='checkbox']").is(':checked')){
        decimals = true
    }
    else{
        decimals = false
    }
    updateTotal();
});

function addPost(post, total) {
    $('.list').append(
    `<div id=${post['id']} class="sortable-child">` +
        `<div class="childItems">` +
        `<div class="row mb-1 mt-1 align-items-center">` +
        `<div class="d-flex col-sm-6 col-md-5 align-items-center mb-1 mb-sm-0">` +
        `<input type="text" class="fw-bold input-sm nameinput form-control mb-0 d-none d-sm-block" value="${post['name']}"/>` +
        `<p class="mb-0">${post["type"]}</p>` +
        `</div>` +
        `<div class="d-none d-sm-block col-sm-6 col-md-1 text-sm-end mb-1 m-md-0">` +
        `<p class="mb-0">${decimalToTime(post["cost"])}</p>` +
        `</div>` +
        `<div class="col-sm-12 col-md-6 d-flex justify-content-end postSelect">` +
        `<select data-option-id=${post['id']} class="form-select">` +
        `</select>` +
        `<a class="btn btn-outline-primary addChildPostButton mb-0" data-bs-toggle="tooltip" data-bs-title="Add child post"><i class="fa-solid fa-plus"></i></a>` +
        `<a class="btn btn-outline-primary addLangButton mb-0" data-bs-toggle="tooltip" data-bs-title="Add language"><i class="fa-solid fa-language"></i></a>` +
        `<a class="btn btn-outline-primary addChanButton mb-0" data-bs-toggle="tooltip" data-bs-title="Add channel"><i class="fa-solid fa-layer-group"></i></a>` +
        `<a class="btn btn-outline-danger deletePost" data-bs-toggle="tooltip" data-bs-title="Delete post"><i class="fa-solid fa-trash"></i></a>` +
        `</div>` +
        `</div>` +
        `</div>`
        )
    mediaPosts.forEach(function (item){
        $(`select[data-option-id="${post['id']}"]`).append(
            `<option value=${item['id']}>${item['name']}</option>`
        );
    });
    updateTotal();
}

function deletePost(post, element) {
    postList.splice(post, 1);
    element.parent().parent().remove();
    updateTotal();
}

function addLang(post) {
    post["languages"] += 1;
    $('#' + post["id"] + ' .childItems').append(
        `<div class="extralang row align-items-center mb-1">` +
        `<div class="col-5">` +
        `<p class="mb-0">Extra Language</p>` +
        `</div>` +
        `<div class="col-1">` +
        `<p class="mb-0">${decimalToTime(post["langcost"])}</p>` +
        `</div>` +
        `<div class="col-6 justify-content-end btn-group btn-group-sm">` +
        `<a class="btn btn-outline-danger deleteLang" data-bs-toggle="tooltip" data-bs-title="Delete language"><i class="fa-solid fa-trash"></i></a>` +
        `</div>` +
        `</div>`);
    updateTotal();
}

function deleteLang(post, element) {
    post["languages"] -= 1;
    element.remove();
    updateTotal();
}

function addChan(post) {
    post["channels"] += 1;
    $('#' + post["id"] + ' .childItems').append(
        `<div class="extrachan row align-items-center mb-1">` +
        `<div class="col-5">` +
        `<p class="mb-0">Extra Channel</p>` +
        `</div>` +
        `<div class="col-1">` +
        `<p class="mb-0">${decimalToTime(post["chancost"])}</p>` +
        `</div>` +
        `<div class="col-6 justify-content-end btn-group btn-group-sm">` +
        `<a class="btn btn-outline-danger deleteChan" data-bs-toggle="tooltip" data-bs-title="Delete channel"><i class="fa-solid fa-trash"></i></a>` +
        `</div>` +
        `</div>`);
    updateTotal();
}

function deleteChan(post, element) {
    post["channels"] -= 1;
    element.remove();
    updateTotal();
}

function addChildPost(post, childPost){
    post['childPosts'].push(childPost);

    $('#' + post['id']).append(
        `<div class="row extraChildPost align-items-center mb-1">` +
        `<div class="col-5">` +
        `<p class="mb-0"><span class="mb-0 fw-bold">Extra post</span>: ${childPost['name']}</p>` +
        `</div>` +
        `<div class="col-1">` +
        `<p class="mb-0">${decimalToTime(childPost['baseCost'])}</p>` +
        `</div>` +
        `<div class="col-6 justify-content-end btn-group btn-group-sm">` +
        `<a class="btn btn-outline-danger deleteChildPost" data-id="${childPost['id']}" data-bs-toggle="tooltip" data-bs-title="Delete child post"><i class="fa-solid fa-trash"></i></a>` +
        `</div>`
    );
    updateTotal();
}

function deleteChildPost(post, element, childPost){
    post["childPosts"].splice(childPost, 1)
    element.remove();
    updateTotal();
}

function updateTotal() {
    if(postList.length === 0){
        $('.resultdiv').addClass('d-none')
    }
    else{
        $('.resultdiv').removeClass('d-none')
    }

    totalTime = 0;
    totalPrice = 0;
    $('.totalresult').empty();
    $('.buttondiv').empty();
    if (postList.length !== 0) {
        postList.forEach(updateTotalCost);
        const PM = totalTime * (PMval / 100);
        const subTotal = totalTime + PM + 0.25;

        $('.totalresult').append(
            `<tr>` +
                `<td>Content strategy</td>` +
                `<td class="text-end">${decimalToTime(0.25)}</td>` +
            `</tr>` +
            `<tr>` +
                `<td class="d-flex align-items-center">PM <input id="pminput" min="0" max="100" type="number" class="input-sm form-control mb-0" value="${PMval}"/> %</td>` +
                `<td class="text-end">${decimalToTime(PM.toFixed(2))} hours</td>` +
            `</tr>` +
            `<tr>` +
                `<td class="fw-bold">Total hours</td>` +
                `<td class="text-end">${decimalToTime(subTotal.toFixed(2))} hours</td>` +
            `</tr>` +
            `<tr>` +
                `<td class="fw-bold">Total price</td>` +
                `<td class="text-end">${totalPrice} &euro;</td>` +
            `</tr>`
        );
        $('.buttondiv').append(
            `<div class="d-flex justify-content-between">` +
            `<a class="btn btn-success" id="exportcsv"><i class="fa-solid fa-file-export me-1"></i>Export to csv</a>` +
            `<form method="POST" action="/">` +
            `<input name="data" type="hidden" value='${JSON.stringify(postList)}'>` +
            `<input name="hash" type="hidden" value='${hash}'>` +
            `<input name="name" type="hidden" value='${title}'>` +
            `<button type="submit" class="btn btn-primary" id="shareposts"><i class="fa-solid fa-share-from-square me-1"></i>Share</button>` +
            `</form>` +
            `</div>`
        );
    }
}

function updateTotalCost(item) {
    let childPostCost = 0;
    let childPostCount = 0;
    item['childPosts'].forEach(function (item){
        childPostCost += parseFloat(item['baseCost']);
        childPostCount += 1;
    })
    const channelCost = item["channels"] * item["chancost"];
    const languageCost = item["languages"] * item["langcost"];
    const time = item["cost"] + languageCost + channelCost + childPostCost;
    totalTime += time;
    totalTime.toFixed(2);
    totalPrice += (math.ceil(time * 2) / 2) * item["rate"];
    $('.totalresult').append(
            `<tr>`+
                `<td class="fw-bold">${item['name']}</td>` +
                `<td></td>` +
            `</tr>` +
            `<tr>`+
                `<td>${item['type']}</td>` +
                `<td class="text-end">${decimalToTime(item["cost"])} hours</td>` +
            `</tr>` +
            `<tr>`+
                `<td>Extra languages - ${item["languages"]}</td>` +
                `<td class="text-end">${decimalToTime(languageCost.toFixed(2))} hours</td>` +
            `</tr>` +
            `<tr>`+
                `<td>Extra channels - ${item["channels"]}</td>` +
                `<td class="text-end">${decimalToTime(channelCost.toFixed(2))} hours</td>` +
            `</tr>` +
            `<tr>` +
                `<td>Extra posts - ${childPostCount}</td>` +
                `<td class="text-end">${decimalToTime(childPostCost.toFixed(2))} hours</td>` +
            `</tr>` +
            `<tr>`+
                `<td>Total</td>` +
                `<td class="text-end">${decimalToTime(time.toFixed(2))} hours</td>` +
            `</tr>` +
            `<tr class="last-item">`+
                `<td>Rate</td>` +
                `<td class="text-end">${(math.ceil(time * 2) / 2) * item["rate"]} &euro;</td>` +
            `</tr>`
    );
}

function decimalToTime(number) {
    if(!decimals){
        const sign = number < 0 ? "-" : "";
        const min = Math.floor(Math.abs(number));
        const sec = Math.floor((Math.abs(number) * 60) % 60);
        return sign + (min < 10 ? "0" : "") + min + ":" + (sec < 10 ? "0" : "") + sec;
    }
    return number;
}

function writeCsvLine(item) {
    let childPostCost = 0;
    item['childPosts'].forEach(function (item){
        childPostCost += parseFloat(item['cost']);
    })
    const totalChannels = item["channels"] * item['chancost'];
    const totalLanguages = item["languages"] * item['langcost'];
    const total = item["cost"] + (item["languages"] * item["langcost"]) + (item["channels"] * item["chancost"]);
    const rate = (math.ceil(total * 2) / 2) * item["rate"];

    csv += `${item['name']},${item['type']},${decimalToTime(item["cost"])},€${rate},${decimalToTime(totalLanguages)},${decimalToTime(totalChannels)},${decimalToTime(childPostCost)},${decimalToTime(total)}\n`;
}

function changeName(title){
    if(title.length !== 0){
        $('#resultnametext').text(title);
        $('#resultnametext').removeClass('d-none');
        $('.resulttitle').addClass('mb-0');
    }
    else{
        $('#resultnametext').addClass('d-none');
        $('.resulttitle').removeClass('mb-0');
    }
    $('input[name="name"]').val(title);
}
function loadTooltips(){
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
}