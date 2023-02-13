
if (typeof id_region === 'undefined' || id_region === null) {
    var id_region = 0;
}
if (typeof mesin === 'undefined' || mesin === null) {
    var mesin = '';
}
$(document).ready(function () {

    $('.table-only').DataTable({
        paging: false,
        ordering: false,
        info: false,
        bFilter: false
    });

    $('#tabel_active').DataTable({
        searchPanes: {
            layout: 'columns-2'
        },
        dom: 'Plfrtip',
        columnDefs: [
            {
                searchPanes: {
                    show: true
                },
                targets: [ 1, 2 ]
            },
            {
                searchPanes: {
                    show: false
                },
                targets: [ 0, 3, 4, 5, 6, 7, 8 ]
            }
        ],
        language: {
            decimal: ",",
            thousands: "."
        }
    });
    $('#tabel_active_wrapper #tabel_active_length').css('float', 'left');
    $('#tabel_active_wrapper #tabel_active_filter').css('float', 'right');

    $('#tabel_produksi').DataTable({
        processing: true,
        serverSide: true,
        order: [ [ 6, "desc" ] ],
        ajax: {
            url: base_url + 'ProduksiNew/ajaxRegion/' + id_region,
            type: 'GET',
            data: {
                tglStart: $('#start').val(),
                tglEnd: $('#end').val(),
                BP_ID: $('#BP_ID').val()
            },
        },
        "drawCallback": function (settings) {
            // Here the response
            var response = settings.json;
            console.log(response);
        },
    });

    var t0 = 0;
    var t1 = 0;
    var duration = 0;
    $('#tabel_produksi_cmd_batch')
        .on('preXhr.dt', function ( e, settings, data ) {
        console.log("fire");
        t0 = performance.now();
    })
    .on('xhr.dt', function ( e, settings, json, xhr, data ) {
        t1 = performance.now();
        duration = ((t1 - t0) / 1000.0).toFixed(2);
        console.log( 'Search took: ' + duration + " seconds." );
    })  
    .DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [ [ 8, "desc" ] ],
        ajax: {
            url: base_url + 'produksi/ajaxRegion/' + id_region,
            type: 'GET',
            data: {
                tglStart: $('#start').val(),
                tglEnd: $('#end').val(),
                BP_ID: $('#BP_ID').val(),
                jo: $('#jo').prop('checked') ? 1 : 0,
                sklp: $('#sklp').prop('checked') ? 1 : 0,
                mesin: mesin
            },
        },
        "drawCallback": function (settings) {
            // Here the response
            var response = settings.json;
            // console.log(response);
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-md-flex justify-content-end'Bf>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            'csv', 'print'
        ]
    });

    $('#tabel_jobmix').DataTable({
        processing: true,
        serverSide: true,
        order: [ [ 0, "desc" ] ],
        ajax: {
            url: base_url + 'Jobmix/ajaxRegion/' + id_region,
            type: 'GET',
            data: {
                mesin: mesin
            },
        },
        "drawCallback": function (settings) {
            // Here the response
            var response = settings.json;
            console.log(response);
        },
    });

    $('#tabel_history').DataTable({
        processing: true,
        serverSide: true,
        order: [ [ 6, "desc" ] ],
        ajax: {
            url: base_url + 'Api/ajaxHistory',
            type: 'GET',
            data: {
                tglStart: $('#start').val(),
                tglEnd: $('#end').val(),
            },
        },
        "drawCallback": function (settings) {
            // Here the response
            var response = settings.json;
            console.log(response);
        },
        columnDefs: [
            {
                render: function (data, type, full, meta) {
                    return "<div style='white-space: normal'>" + data + "</div>";
                },
                targets: [ 8, 3 ]
            },
            { orderable: false, targets: 10 } ]
    });
});