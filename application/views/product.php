		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Products</title>
			<!-- Latest compiled and minified CSS -->
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
		</head>
		<body>
			<div>
				<button type="button" class="btn btn-primary" id="openModal" data-toggle="modal" data-target="#exampleModal" onclick="openModalFunc()">+</button>
				<table class="table" id="tableProduct">
					<tr>
						<th>#</th>
						<th>Product Name</th>
						<th>Product Price</th>
						<th>Action</th>
					</tr>
				</table>
			</div>
			<!-- Button trigger modal -->
		

		<!-- Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Product</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
			        </button>
		      </div>
		      <div class="modal-body">
		        <form method="post" enctype="multipart/form-data" id="formProduct">
		        	<input type="hidden" name="action" id="action" value="insert">
		        	<input type="hidden" name="product_id" id="product_id" value="0">
				  <div class="form-group">
				    <label for="exampleInputEmail1">Product Name</label>
				    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter Product Name" required="required" value="">
				  </div>
				  <div class="form-group">
				    <label for="exampleInputEmail1">Product Price</label>
				    <input type="text" class="form-control" id="product_price" name="product_price" placeholder="Enter Product Name" required="required" value="">
				  </div>
				  <div class="form-group">
				    <label for="exampleInputEmail1">Product Description</label>
				    <input type="text" class="form-control" id="product_descr" name="product_descr" placeholder="Enter Product Name" required="required" value="">
				  </div>
				  <div class="form-group">
				    <label for="exampleInputPassword1">File</label>
				    <input type="file" class="form-control" id="product_img" name="product_img[]" multiple placeholder="Product Images" name="product_img">
				  </div>
				 
				</form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		         <button type="button" id="addProduct" class="btn btn-primary">Add Product</button>
		      </div>
		    </div>
		  </div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Product Images</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
			        </button>
		      </div>
		      <div class="modal-body" id="imgContainer">
		        
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		         <button type="button" id="addProduct" class="btn btn-primary">Add Product</button>
		      </div>
		    </div>
		  </div>
		</div>
			

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>

		<script>
				const baseUrl = `<?=base_url();?>`;
			$(document).ready(function() {

				loadProducts();
					// alert(baseUrl);
				$("#addProduct").click(function(){
					let product_name=$("#product_name").val();
					let product_price=$("#product_price").val();
					let product_descr=$("#product_descr").val();
					let product_img=$("#product_img").val();
					let action=$("#action").val();
					let product_id = $("#product_id").val();
					$.ajax({
						method:"POST",
						url:baseUrl+'productOperation',
						data:{action:action,product_name:product_name, product_price:product_price, product_descr:product_descr, product_img:product_img,product_id:product_id},
						dataType:"JSON",
						success:function(result){
							if (result.status == 200) {
								// alert(result.body);
								$("#exampleModal").modal("hide");
								// if (action == 'insert') {
									let lastProductId = result.lastProductId;
									var files = $('#product_img')[0].files;
									// alert(files.length);	
									if (files.length > 0) {
										uploadProductImg(lastProductId,product_img,files.length);
									}
								// }
								$("#tableProduct").dataTable().fnDestroy();
								loadProducts();
							}else{
								alert(result.body);
								$("#exampleModal").modal("hide");
							}
							
						}
					})
				})

		// alert("test");
			
			})

			function  viewImages(producId){
				$("#exampleModal2").modal("show");
				getProuctImages(producId).then((data) =>{
					console.log(data);
					 $("#imgContainer").empty().html(data.images);
				});
			}

			function getProuctImages(producId){
				return new Promise(function(resolve, reject) {
				 
					$.ajax({
			            type:'POST',
			            url: baseUrl+'getProuctImages',
			            data:{product_id:producId},
			            dataType:'JSON',
			            success:function(data){
			            	resolve(data);
			            	// console.log(data.imagesArr);	
			             //    $("#imgContainer").empty().html(data.images);
			            },
			            error: function(data){
			                
			            	reject(data);
			            }
			        });
				});
			}

			function uploadProductImg(lastProductId,product_img,filesCount){
				var formData = new FormData();
				// formData.set('product_img[]',$('input[type=file]')[0].files[0]);
				formData.set('lastProductId', lastProductId);
				formData.set('filesCount', filesCount);
				// Attach file
				// formData.append('image', $('input[type=file]')[0].files[0]); 
                //following  code is working fine in for single image upload
                    //this code not working for multiple image upload           
                var names = [];
                var file_data = $('input[type="file"]')[0].files; // for multiple files
			    for(var i = 0;i<file_data.length;i++){
			    formData.set("product_img_"+i, file_data[i]);
				}
				formData.set('product_img[]',names);

		        $.ajax({
		            type:'POST',
		            url: baseUrl+'uploadProductImg',
		            data:formData,
		            cache:false,
		            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
    				processData: false, 
		            success:function(data){
		                console.log("success");
		                console.log(data);
		            },
		            error: function(data){
		                console.log("error");
		                console.log(data);
		            }
		        });
			}
			function openModalFunc(){
				$("#formProduct")[0].reset();
				$("#action").val("").val("insert");
				$("#product_id").val("").val("0");
			}

			function loadProducts(){
				if ( $.fn.dataTable.isDataTable( '#tableProduct' ) ) {
				    $('#tableProduct').DataTable({
			        processing: true,
			        serverSide: true,
			        ajax: {
			            url: baseUrl+'getProducts',
			            type: 'POST',
			        },
			        columns: [
			            { data: 'id' },
			            { data: 'product_name' },
			            { data: 'product_price' },
			            { data: 'product_descr' },
			            { data: 'action' }
			        ],
			    });
				}
				else {
				    $('#tableProduct').DataTable({
			        processing: true,
			        paging:false,
			        serverSide: true,
			        ajax: {
			            url: baseUrl+'getProducts',
			            type: 'POST',
			        },
			        columns: [
			            { data: 'id' },
			            { data: 'product_name' },
			            { data: 'product_price' },
			            { data: 'product_descr' },
			            { data: 'action' }
			        ],
			    });
				}
				
			}

			function productAction(productId,action){
				if (action == 'delete') {
					if(confirm("Are you sure to delete This Item ?")){
						$.ajax({
							method:"POST",
							url:baseUrl+'productOperation',
							data:{action:action,product_id:productId},
							dataType:"JSON",
							success:function(result){
								if (result.status == 200) {
									$("#tableProduct").dataTable().fnDestroy();
									loadProducts();
								}else{
									alert("Something went wrong");
								}
							}
						})
					}else{

					}
				}else{
					$.ajax({
						method:"POST",
						url:baseUrl+'getProductToEdit',
						data:{action:action,product_id:productId},
						dataType:"JSON",
						success:function(result){
							if (result.status == 200) {
								$("#openModal").click();
								let res = result.data;
								$("#action").val("edit");
								$("#product_id").val(productId);
								$("#product_name").val(res.product_name);
								$("#product_price").val(res.product_price);
								$("#product_descr").val(res.product_descr);
								$("#addProduct").text("Update Product");
							}else{
								alert("Something went wrong");
							}
						}
					})
				}
			}
		</script>
		</body>
		</html>
