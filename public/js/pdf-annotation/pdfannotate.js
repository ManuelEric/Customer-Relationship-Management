/**
 * PDFAnnotate v1.0.1
 * Author: Ravisha Heshan
 */

var PDFAnnotate = function (container_id, url, options = {}) {
	this.number_of_pages = 0;
	this.pages_rendered = 0;
	this.active_tool = 1; // 1 - Free hand, 2 - Text, 3 - Arrow, 4 - Rectangle
	this.fabricObjects = [];
	this.fabricObjectsData = [];
	this.color = '#000';
	this.borderColor = '#000';
	this.borderSize = 1;
	this.font_size = 16;
	this.active_canvas = 0;
	this.container_id = container_id;
	this.url = url;
	this.pageImageCompression = options.pageImageCompression
		? options.pageImageCompression.toUpperCase()
		: "NONE";
	var inst = this;

	var loadingTask = pdfjsLib.getDocument(this.url);
	loadingTask.promise.then(function (pdf) {
		var scale = options.scale ? options.scale : 1.3;
		inst.number_of_pages = pdf.numPages;

		for (var i = 1; i <= pdf.numPages; i++) {
			pdf.getPage(i).then(function (page) {
        if (typeof inst.format === 'undefined' ||
            typeof inst.orientation === 'undefined') {
            var originalViewport = page.getViewport({ scale: 1 });
            inst.format = [originalViewport.width, originalViewport.height];
            inst.orientation =
              originalViewport.width > originalViewport.height ?
                'landscape' :
                'portrait';
          }

				var viewport = page.getViewport({ scale: scale });
				var canvas = document.createElement('canvas');

				document.getElementById(inst.container_id).appendChild(canvas);
				canvas.className = 'pdf-canvas';
				canvas.height = viewport.height;
				canvas.width = viewport.width;
				context = canvas.getContext('2d');

				// Increase the resolution of the canvas

				// if (window.devicePixelRatio > 1) {
				// 	var canvasWidth = canvas.width;
				// 	var canvasHeight = canvas.height;

				// 	canvas.width = canvasWidth * window.devicePixelRatio;
				// 	canvas.height = canvasHeight * window.devicePixelRatio;
				// 	canvas.style.width = canvasWidth + "px";
				// 	canvas.style.height = canvasHeight + "px";

				// 	context.scale(window.devicePixelRatio, window.devicePixelRatio);
				// }

				var renderContext = {
					canvasContext: context,
					viewport: viewport
				};
				var renderTask = page.render(renderContext);
				renderTask.promise.then(function () {
					$('.pdf-canvas').each(function (index, el) {
						$(el).attr('id', 'page-' + (index + 1) + '-canvas');
					});
					inst.pages_rendered++;
					if (inst.pages_rendered == inst.number_of_pages) inst.initFabric();
				});
			});
		}
	}, function (reason) {
		console.error(reason);
	});

	this.initFabric = function () {
		var inst = this;
		let canvases = $('#' + inst.container_id + ' canvas')
		canvases.each(function (index, el) {
			var background = el.toDataURL("image/png");
			var fabricObj = new fabric.Canvas(el.id, {
				freeDrawingBrush: {
					width: 1,
					color: inst.color
				}
			});
			inst.fabricObjects.push(fabricObj);
			if (typeof options.onPageUpdated == 'function') {
				fabricObj.on('object:added', function () {
					var oldValue = Object.assign({}, inst.fabricObjectsData[index]);
					inst.fabricObjectsData[index] = fabricObj.toJSON()
					options.onPageUpdated(index + 1, oldValue, inst.fabricObjectsData[index])
				})
			}
			fabricObj.setBackgroundImage(background, fabricObj.renderAll.bind(fabricObj));
			$(fabricObj.upperCanvasEl).click(function (event) {
				inst.active_canvas = index;
				inst.fabricClickHandler(event, fabricObj);
			});
			fabricObj.on('after:render', function () {
				inst.fabricObjectsData[index] = fabricObj.toJSON()
				fabricObj.off('after:render')
			})

			if (index === canvases.length - 1 && typeof options.ready === 'function') {
				options.ready()
			}
		});
	}

	this.fabricClickHandler = function (event, fabricObj) {
		var inst = this;
		if (inst.active_tool == 2) {
			var text = new fabric.IText('Sample text', {
				left: event.clientX - fabricObj.upperCanvasEl.getBoundingClientRect().left,
				top: event.clientY - fabricObj.upperCanvasEl.getBoundingClientRect().top,
				fill: inst.color,
				fontSize: inst.font_size,
				selectable: true
			});
			fabricObj.add(text);
			inst.active_tool = 0;
		}
	}
}

PDFAnnotate.prototype.enableSelector = function () {
	var inst = this;
	inst.active_tool = 0;
	if (inst.fabricObjects.length > 0) {
		$.each(inst.fabricObjects, function (index, fabricObj) {
			fabricObj.isDrawingMode = false;
		});
	}
}

PDFAnnotate.prototype.enablePencil = function () {
	var inst = this;
	inst.active_tool = 1;
	if (inst.fabricObjects.length > 0) {
		$.each(inst.fabricObjects, function (index, fabricObj) {
			fabricObj.isDrawingMode = true;
		});
	}
}

PDFAnnotate.prototype.enableAddText = function () {
	var inst = this;
	inst.active_tool = 2;
	if (inst.fabricObjects.length > 0) {
		$.each(inst.fabricObjects, function (index, fabricObj) {
			fabricObj.isDrawingMode = false;
		});
	}
}

PDFAnnotate.prototype.enableRectangle = function () {
	var inst = this;
	var fabricObj = inst.fabricObjects[inst.active_canvas];
	inst.active_tool = 4;
	if (inst.fabricObjects.length > 0) {
		$.each(inst.fabricObjects, function (index, fabricObj) {
			fabricObj.isDrawingMode = false;
		});
	}

	var rect = new fabric.Rect({
		width: 100,
		height: 100,
		fill: inst.color,
		stroke: inst.borderColor,
		strokeSize: inst.borderSize
	});
	fabricObj.add(rect);
}

PDFAnnotate.prototype.enableAddArrow = function () {
	var inst = this;
	inst.active_tool = 3;
	if (inst.fabricObjects.length > 0) {
		$.each(inst.fabricObjects, function (index, fabricObj) {
			fabricObj.isDrawingMode = false;
			new Arrow(fabricObj, inst.color, function () {
				inst.active_tool = 0;
			});
		});
	}
}

PDFAnnotate.prototype.addImageToCanvas = function (axis, type) {
	var inst = this;
	var fabricObj = inst.fabricObjects[inst.active_canvas];
	console.log(inst)
	if (fabricObj) {
		var inputElement = document.createElement("input");
		inputElement.type = 'file'
		inputElement.accept = ".jpg,.jpeg,.png,.PNG,.JPG,.JPEG";
		inputElement.onchange = function () {
			var reader = new FileReader();
			reader.addEventListener("load", function () {
				inputElement.remove()
				var image = new Image();
				var widthEntirePage = document.documentElement.scrollWidth;
				
				image.onload = function () {
					var scaleX, scaleY = 1; var width, height = 0;
					var canvas_width = fabricObj.width;
					var canvas_height = fabricObj.height;

					var img = new fabric.Image(image)
					
					var scaleX = (canvas_width / img.width) * 0.18;
					var scaleY = (canvas_height / img.height) * 0.066;
					
					img.setOptions({
						left: canvas_width*0.688,
						top: canvas_height*0.707,
						scaleX: scaleX,
						scaleY: scaleY
					})
					
					// if(type == 'invoice'){
					// 	if(axis != null && axis.type == 'invoice')
					// 	{
					// 		img.setOptions({
					// 			left: axis.left,
					// 			top: axis.top,
					// 			scaleX: axis.scaleX,
					// 			scaleY: axis.scaleY,
					// 			angle: axis.angle,
					// 			flipX: axis.flipX,
					// 			flipY: axis.flipY,
					// 		})
					// 	}
					// }else if(type == 'receipt'){
					// 	if(axis != null && axis.type == 'receipt')
					// 	{
					// 		img.setOptions({
					// 			left: axis.left,
					// 			top: axis.top,
					// 			scaleX: axis.scaleX,
					// 			scaleY: axis.scaleY,
					// 			angle: axis.angle,
					// 			flipX: axis.flipX,
					// 			flipY: axis.flipY,
					// 		})
					// 	}
					// }

					
					fabricObj.add(img)

					window.scrollTo(0, canvas_height*0.5)
				}
				image.src = this.result;
			}, false);
			reader.readAsDataURL(inputElement.files[0]);
		}
		
		document.getElementsByTagName('body')[0].appendChild(inputElement)
		inputElement.click()
	}
}

PDFAnnotate.prototype.deleteSelectedObject = function () {
	var inst = this;
	var activeObject = inst.fabricObjects[inst.active_canvas].getActiveObject();
	if (activeObject) {
		if (confirm('Are you sure ?')) inst.fabricObjects[inst.active_canvas].remove(activeObject);
	}
}

PDFAnnotate.prototype.savePdf = function (method, fileName, route, type) {

	var inst = this;
	var doc = new jspdf.jsPDF();
	if (typeof fileName === 'undefined') {
		fileName = `${new Date().getTime()}.pdf`;
	}

	showLoading();
	inst.fabricObjects.forEach(function (fabricObj, index) {
		if (index != 0) {
			doc.addPage();
			doc.setPage(index + 1);
		}
		doc.addImage(
			fabricObj.toDataURL({
				format: 'png'
			}),
			inst.pageImageCompression == "NONE" ? "PNG" : "JPEG",
			0,
			0,
			doc.internal.pageSize.getWidth(),
			doc.internal.pageSize.getHeight(),
			`page-${index + 1}`,
			["FAST", "MEDIUM", "SLOW"].indexOf(inst.pageImageCompression) >= 0
				? inst.pageImageCompression
				: undefined
		);
		if (index === inst.fabricObjects.length - 1) {
			if (method == 'print') {
				doc.autoPrint();
				doc.save(fileName);
				
			} else {
				var data = doc.output('blob');
				var formData = new FormData();
				var dataImage = JSON.parse(pdf.serializePdf());
				let image_index = 0;
				if(type=='receipt') {
					image_index = 1;
				}

				if(dataImage[0].objects.length === 1){
					formData.append("top", dataImage[0].objects[0].top);
					formData.append("left", dataImage[0].objects[0].left);
					formData.append("scaleX", dataImage[0].objects[0].scaleX);
					formData.append("scaleY", dataImage[0].objects[0].scaleY);
					formData.append("angle", dataImage[0].objects[0].angle);
					formData.append("flipX", dataImage[0].objects[0].flipX);
					formData.append("flipY", dataImage[0].objects[0].flipY);
					formData.append("no_data", 0);
				} else {
					formData.append("no_data", 1);
				}

				formData.append("pdfFile", data, fileName);
				
				axios.post(route, formData)
				.then(function (response) {
					// console.log(response.data);return;

					if(response.data.status == 'success'){
						alert('Document saved successfully')
						swal.close();
					}else{
						alert(response.data.message)
						swal.close();
					}
				}).catch(function (error) {
					console.log(error);
					swal.close();
				});
			}
			// swal.close();
		}
	})
}


PDFAnnotate.prototype.setBrushSize = function (size) {
	var inst = this;
	$.each(inst.fabricObjects, function (index, fabricObj) {
		fabricObj.freeDrawingBrush.width = size;
	});
}

PDFAnnotate.prototype.setColor = function (color) {
	var inst = this;
	inst.color = color;
	$.each(inst.fabricObjects, function (index, fabricObj) {
		fabricObj.freeDrawingBrush.color = color;
	});
}

PDFAnnotate.prototype.setBorderColor = function (color) {
	var inst = this;
	inst.borderColor = color;
}

PDFAnnotate.prototype.setFontSize = function (size) {
	this.font_size = size;
}

PDFAnnotate.prototype.setBorderSize = function (size) {
	this.borderSize = size;
}

PDFAnnotate.prototype.clearActivePage = function () {
	var inst = this;
	var fabricObj = inst.fabricObjects[inst.active_canvas];
	var bg = fabricObj.backgroundImage;
	if (confirm('Are you sure?')) {
		fabricObj.clear();
		fabricObj.setBackgroundImage(bg, fabricObj.renderAll.bind(fabricObj));
	}
}

PDFAnnotate.prototype.serializePdf = function () {
	var inst = this;
	return JSON.stringify(inst.fabricObjects, null, 4);
}



PDFAnnotate.prototype.loadFromJSON = function (jsonData) {
	var inst = this;
	$.each(inst.fabricObjects, function (index, fabricObj) {
		if (jsonData.length > index) {
			fabricObj.loadFromJSON(jsonData[index], function () {
				inst.fabricObjectsData[index] = fabricObj.toJSON()
			})
		}
	})
}

function showLoading()
{
	Swal.fire({
		width: 100,
		backdrop: '#4e4e4e7d',
		allowOutsideClick: false,
	})
	Swal.showLoading();
}