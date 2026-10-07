<style>

.product-content {
    border: 1px solid #dfe5e9;
    margin: 12px 0;
    background: #fff
}

.product-image {
    background: #fff;
    min-height: 238px;
    overflow: hidden;
    position: relative
}

.product-deatil {
    padding: 30px;
    /* border-bottom: 1px solid #dfe5e9 */
}

.product-deatil h2.name {
    margin: 0
}

.product-deatil .price-container {
    font-size: 24px;
    font-weight: 300
}

.product-deatil .certified ul {
    padding-left: 0;
    list-style: none;
    display: flex;
    gap: 10px
}

.product-deatil .certified ul li {
    background: #f9f9f9;
    border: 1px solid #ccc;
    padding: 13px 19px
}

.product-deatil .certified ul li a {
    color: #6d7a83;
    font-size: 12px;
    text-decoration: none
}

.product-deatil .certified ul li a span {
    display: block;
    color: #21c2f8;
    font-size: 13px;
    font-weight: 700;
    text-align: center
}

.description-tabs {
    margin-top: 20px
}

.description-tabs .nav-link {
    font-weight: 500
}

#previewImage {
    max-width: 100%;
    height: auto
}

.img-carousel {
    width: 700px;
    height: 400px;
    cursor: pointer
}

.serial {
    font-size: 16px;
}

/* ==== Mobile tweaks ===== */
@media (max-width:575.98px) {
    .modal-dialog.modal-xl {
        max-width: 100%;
        margin: 0
    }

    .product-deatil {
        padding: 20px
    }
}

#table-scroll {
  height:250px;
  overflow:auto;  
  /* margin-top:20px; */
}
</style>

<!-- Product Modal -->
<div id="productInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="productInfoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="productInfoModalLabel">Product Details</h4>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="product-content product-wrap clearfix">
                    <div class="row">
                        <input type="hidden" id="id">
                        <!-- Image Carousel -->
                        <div class="col-md-5 col-12">
                            <div class="product-image">
                                <div id="productCarousel" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <li data-target="#productCarousel" data-slide-to="0" class="active"></li>
                                        <li data-target="#productCarousel" data-slide-to="1"></li>
                                        <li data-target="#productCarousel" data-slide-to="2"></li>
                                    </ol>
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="https://www.bootdey.com/image/700x400/FFB6C1/000000"
                                                class="d-block w-100 img-carousel" alt="Pink Sample"
                                                data-title="Pink Sample" />
                                        </div>
                                        <!-- <div class="carousel-item">
                                            <img src="https://www.bootdey.com/image/700x400/87CEFA/000000"
                                                class="d-block w-100 img-carousel" alt="Blue Sample"
                                                data-title="Blue Sample" />
                                        </div>
                                        <div class="carousel-item">
                                            <img src="https://www.bootdey.com/image/700x400/B0C4DE/000000"
                                                class="d-block w-100 img-carousel" alt="Lavender Sample"
                                                data-title="Lavender Sample" />
                                        </div> -->
                                    </div>
                                    <a class="carousel-control-prev" href="#productCarousel" role="button"
                                        data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#productCarousel" role="button"
                                        data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-3 mb-3 row">
                                <div class="col-md-6">
                                <input type="file" id="uploadInput" accept="image/*" class="form-control-file ">
                                </div>
                                <div class="col-md-3">
                                <button class="btn btn-primary btn-sm mt-0"
                                    id="uploadBtn"><i class="fa fa-upload"></i> Upload</button>
                                    </div>
                                <div id="uploadStatus" class="small mt-1"></div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="col-md-7  col-12">
                            <div class="product-deatil">
                                <h2 class="name">
                                    Product Name Title
                                    <small class="d-block">Serial:  <a href="#">Adeline</a></small>
                                    <span class="text-primary">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star"></i><i class="fa fa-star text-muted"></i>
                                    </span>
                                    <small>(109 Votes)</small>
                                </h2>

                                <!-- <h3 class="price-container mt-3">
                                    $129.54 <small class="text-muted">*includes tax</small>
                                </h3>

                                <div class="certified mt-3">
                                    <ul>
                                        <li><a href="#">Delivery<span>7 Days</span></a></li>
                                        <li><a href="#">Certified<span>Assured</span></a></li>
                                    </ul>
                                </div> -->

                                <!-- Tabs -->
                                <div class="description-tabs">
                                    <ul class="nav nav-pills" id="productTab" role="tablist">
                                    <li class="nav-item"><a class="nav-link active" id="address-tab" data-bs-toggle="tab"
                                    href="#address" role="tab">Address</a></li>
                                        <li class="nav-item"><a class="nav-link " id="desc-tab" data-bs-toggle="tab"
                                                href="#description" role="tab">Description</a></li>
                                        <li class="nav-item"><a class="nav-link" id="specs-tab" data-bs-toggle="tab"
                                                href="#specs" role="tab">Specifications</a></li>
                                        
                                        <li class="nav-item"><a class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                                href="#reviews" role="tab">Reviews</a></li>
                                        <li class="nav-item"><a class="nav-link" id="location-tab" data-bs-toggle="tab"
                                                href="#location" role="tab">Location</a></li>
                                    </ul>
                                    <hr>
                                    <div class="tab-content mt-3" id="productTabContent">
                                        <div class="tab-pane fade " id="description" role="tabpanel">Detailed
                                            product description here.</div>
                                        <div class="tab-pane fade" id="specs" role="tabpanel">
                                       
                                        </div>
                                        <div class="tab-pane fade show active" id="address" role="tabpanel">
                                           No data
                                        </div>
                                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                                        
                                        </div>
                                        <div class="tab-pane fade locationCls" id="location" role="tabpanel">
                                            <!-- <p>No map yet.</p> -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <!-- <div class="mt-4 d-flex justify-content-between">
                                    <a href="#" class="btn btn-success btn-lg">Add to cart ($129.54)</a>
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary"><i class="fa fa-star"></i>
                                            Wishlist</button>
                                        <button class="btn btn-outline-secondary"><i class="fa fa-envelope"></i>
                                            Contact</button>
                                    </div>
                                </div> -->
                            </div> <!-- /product-deatil -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="previewTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle"></h5>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Preview" class="img-fluid">
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger" id="deleteImageBtn">
                    <i class="fa fa-trash"></i> Delete Image
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JS Scripts for Bootstrap -->

<script>
$(document).on('click', '.productInfo', function (e) {
    e.preventDefault();

    const id = $(this).data('id');
    $('#id').val(id);                                           // stash for uploads

    $.post({
        url: "{{ route('getProductInfo') }}",
        data: { _token: CSRF_TOKEN, id },
        dataType: 'json',
        success: function (res) {
            // console.log(res[0].goe_location.latitude);
            if (!res[0].success) { return alert('Invalid ID'); }

            /* -- 1. Convert Laravel’s JSON → gallery array & productInfo object -- */
            const gallery = res[0].images.map(img => ({
                src  : 'productImages/' + img.name,
                title: img.name.replace(/\.[^.]+$/, '')          // strip extension
            }));

            const p = res[0].prodcutInfo[0];                               // shorter alias
            // console.log(p);
            const productInfo = {
                name       : p.category +' - '+ p.model,
                brand      : p.serial_no,
                votes      : p.votes         ?? 0,
                rating     : p.rating        ?? 0,
                price      : parseFloat(p.price),
                currency   : p.currency      ?? '₹',
                taxNote    : p.tax_note      ?? '',
                images     : gallery,
                delivery   : p.delivery_time ?? '',
                certified  : p.certified     ?? '',
                description: p.note   ?? '',
                specs      : (p.specs ?? '').split('|'),
                reviews    : p.reviews       ?? []               // add if you join reviews
            };

            if (res[1]!='') {
                $('#specs').html(res[1]);/// html part replacement append
             }
            // alert(p.vendor_name)
            var address ='No data';
        if (p.vendor_name!=null) {// for address append

          
            var address = `
               <div style="line-height: 2; font-family: Arial, sans-serif;">
                    <h4 style="margin-bottom: 0;">${p.vendor_company ?? p.vendor_company2 ?? ''}</h4>
    <div>${p.vendor_address ?? ''}</div>
    <div>
      ${p.vendor_city ?? ''}, ${p.vendor_district ?? ''}, ${p.vendor_state ?? ''} - ${p.vendor_pincode ?? ''}
    </div>
                    <strong>POC:</strong> ${p.vendor_name ?? ''}<br>
                    <strong>Mobile:</strong> ${p.vendor_mobile1 ?? ''}, 
                    <strong>Phone:</strong> ${p.vendor_mobile2 ?? ''}
                </div>
            `;
        }           
                $('#address').html(address);/// html part replacement append

                // this is for location map enable//////////////////////////////////////////////////////////////////
        var location ="No data";
        if (res[0].goe_location.latitude==null) {
            var location =`<input type="text"  class="form-control location" required/><button class="btn-primary btn-sm float-right mt-1" onclick="updateLocation(${p.vendor_id ?? ''},1)">Update Location</button>`;

        } else {
            var location = `<iframe width="100%" height="250" style="border:0;"  loading="lazy" allowfullscreen  
            src="https://maps.google.com/maps?q=${res[0].goe_location.latitude ?? ''},${res[0].goe_location.longitude ?? ''}&z=14&output=embed"> </iframe>
            <button class="btn btn-danger btn-sm float-right" onclick="updateLocation(${p.vendor_id ?? ''},2)">X</button> &nbsp;<button onclick="shareContent()" class="btn btn-primary btn-sm float-right"><i class="fa fa-share-alt"></i></button>`;
        }
        
        $('.locationCls').html(location);
        ////////////////////////////////////////////////////////
            /* -- 2. Paint the modal & open it -- */
            loadProduct(productInfo);                            // <— your existing painter
            $('#productInfoModal').modal('show');
        },
        error: () => alert('Server error, please retry')
    });
});


// var productInfo = {
//     name: 'Wireless Headphones X1',
//     brand: 'AudioMax',
//     votes: 214,
//     rating: 4,
//     price: 199.99,
//     currency: '$',
//     taxNote: '*includes tax',
//     images: [{
//         src: 'https://www.bootdey.com/image/700x400/9400D3/000000',
//         title: 'Purple Angle'
//     }, {
//         src: 'https://www.bootdey.com/image/700x400/00BFFF/000000',
//         title: 'Side View'
//     }, {
//         src: 'https://www.bootdey.com/image/700x400/FFFF00/000000',
//         title: 'Accessories'
//     }],
//     delivery: '7 Days',
//     certified: 'Quality Assured',
//     description: 'High‑fidelity wireless headphones with active noise cancellation and 40‑hour battery life.',
//     specs: ['Bluetooth 5.3', 'ANC – 30dB', '40 hours playback', 'USB‑C Fast charge'],
//     reviews: [{
//         user: 'Alice',
//         text: 'Amazing sound!',
//         rating: 5
//     }, {
//         user: 'Ben',
//         text: 'Very comfortable.',
//         rating: 4
//     }]
// };
function shareContent() {
    if (navigator.share) {
    navigator.share({
      title: 'Location',
      text: 'Check out this place:',
      url: 'https://maps.app.goo.gl/qQZXMMGBN9ZtCFnw7'
    })
    .then(() => console.log('Shared successfully'))
    .catch((error) => console.log('Error sharing:', error));
  } else {
    alert('Sharing is not supported on this device.');
  }
}
function updateLocation(vendor_id,type) {
    // alert(vendor_id);
    var goe_location = $('.location').val();
    var reason = null;
    if (type==2) {
        confirm("Are you sure you want to delete this record?");
        var reason = prompt("Please enter reason for deletion:");
    }
        // AJAX request
        if (goe_location=='') {
          alert('Please fill the fields');
          return false;  
        }
        $.ajax({
            url: "{{ route('updateLocation') }}",
            type: 'post',
            data: {
                _token: CSRF_TOKEN,
                vendor_id: vendor_id,
                goe_location: goe_location,
                type: type,
                reason: reason,
            },
            success: function (res) {
                // console.log(res);
                if (res.latitude !='') {
                    var location = `<iframe width="550" height="250" style="border:0;"  loading="lazy" allowfullscreen  
                src="https://maps.google.com/maps?q=${res.latitude ?? ''},${res.longitude ?? ''}&z=14&output=embed"></iframe>
                <button class="btn btn-danger btn-sm float-right" onclick="updateLocation(${vendor_id},2)">X</button> &nbsp;
                <button class="btn btn-primary btn-sm float-right"><i class="fa fa-share-alt"></i></button>`;
                } else {
                    var location =`<input type="text"  class="form-control location" required/><button class="btn-primary btn-sm float-right mt-1" onclick="updateLocation(${vendor_id},1)">Update Location</button>`;

                }
                

                $('.locationCls').html(location);
            }
        });
}

function starIcons(r) {
    var h = '';
    for (let i = 1; i <= 5; i++) h += '<i class="fa ' + (i <= r ? 'fa-star' : 'fa-star text-muted') + '"></i>';
    return h;
}

function buildCarousel(imgs) {
    var ind = '',
        inn = '';
if (imgs!='') {
    imgs.forEach(function(i, x) {
        ind += '<li data-target="#productCarousel" data-slide-to="' + x + '" class="' + (x == 0 ? 'active' :
            '') + '"></li>';
        inn += '<div class="carousel-item ' + (x == 0 ? 'active' : '') + '"><img src="' + i.src +
            '" class="d-block w-100 img-carousel" alt="' + i.title + '" data-title="' + i.title +
            '"></div>';
    });
    $('#productCarousel .carousel-indicators').html(ind);
    $('#productCarousel .carousel-inner').html(inn);
}
}

function loadProduct(p) {
    $('#productInfoModal .name').html(p.name + '<small class="d-block serial">Serial:  <a href="{{ route("search") }}?serialNo=' + p.brand+'">' + p.brand +
        '  <i class="fa fa-long-arrow-right"></i> Click to ticket info</a></small><span class="text-primary serial">' + starIcons(p.rating) + '</span><small>(' + (p.votes || 0) +
        ' Votes)</small>');
    $('#productInfoModal .price-container').html(p.currency + p.price + ' <small class="text-muted">' + p.taxNote +
        '</small>');
    $('#addToCartBtn').text('Add to cart (' + p.currency + p.price + ')');
    $('#certifiedBlock ul').html('<li><a href="#">Delivery<span>' + p.delivery +
        '</span></a></li><li><a href="#">Certified<span>' + p.certified + '</span></a></li>');
    $('#certifiedBlock').removeClass('d-none');
    $('#description').text(p.description);
    
    //$('#specs').html('<ul>' + p.specs.map(function(s) {
       // return '<li>' + s + '</li>';
    //}).join('') + '</ul>');
    $('#reviews').html(p.reviews.map(function(r) {
       return '<p><strong>' + r.user + ':</strong> ' + r.text + ' ' + starIcons(r.rating) + '</p>';
    }).join('') || '<p>No reviews yet.</p>');
    buildCarousel(p.images);
    // console.log(p.images)
;}
$(document).on('click', '.img-carousel', function() {
    var s = $(this).attr('src'),
        t = $(this).data('title') || $(this).attr('alt');
    $('#previewImage').attr('src', s);
    $('#previewTitle').text(t);
    $('#imagePreviewModal').modal('show');
});
$('#deleteImageBtn').on('click', function() {
    var s = $('#previewImage').attr('src');
    info.images = info.images.filter(function(i) {
        return i.src !== s
    });
    buildCarousel(info.images);
    $('#imagePreviewModal').modal('hide');
});
$('.open-product-modal').on('click', function() {
    loadProduct(info);
    $('#productInfoModal').modal('show');
});

// ========= IMAGE UPLOAD =========
$('#uploadBtn').on('click', function() {
    var id = $('#id').val();
    var file = $('#uploadInput')[0].files[0];
    if (!file) {
        $('#uploadStatus').removeClass().addClass('text-danger').text('No file selected');
        return;
    }
    var fd = new FormData();
    fd.append('image', file);
    fd.append('id', id);
    $('#uploadStatus').removeClass().text('Uploading...');
    $.ajax({
        url: 'upload-image',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: fd,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#uploadStatus').removeClass().addClass('upload-ok').text('Stored');
                productInfo.images.push({
                    src: res.url,
                    title: file.name
                });
                buildCarousel(productInfo.images);
                $('#uploadInput').val('');
            } else {
                $('#uploadStatus').removeClass().addClass('upload-fail').text('Failed');
            }
        },
        error: function() {
            $('#uploadStatus').removeClass().addClass('upload-fail').text('Failed');
        }
    });
});
</script>
</body>

</html>