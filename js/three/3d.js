$(function() {
    var container, container_stat, scene, camera, renderer, container_stat, orbitControls, cube;
    var planeGeometry, boxMaterials;
    var scale = 0.001;
    var step = 0;
    var controls = new function () {
	this.rotationSpeed = 0.02;
	this.bouncingSpeed = 0.03;
    }
    container	    = document.getElementById('webGL-container');
    container_stat  = document.getElementById('Stats-output');
    //проверка поддержки WebGL
    if (Detector.webgl) {
	init();
	render();
    } else {
	var warning = Detector.getWebGLErrorMessage();
	container.appendChild(warning);
    }

    function init() { 
	scene = new THREE.Scene();
	camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 100);
	renderer = new THREE.WebGLRenderer({antialias: true, alpha: true});
	//renderer = new THREE.WebGLRenderer();
	renderer.setPixelRatio(window.devicePixelRatio);
	renderer.setClearColor(0xF7F7F7, 1.0);
	//renderer.setClearColor(0xEDEDED, 1.0);
	renderer.setSize(window.innerWidth, window.innerHeight);
	renderer.shadowMap.enabled = true;//включить тени
	container.appendChild(renderer.domElement);

	addControls();
	addAxes(5);
	addStats();
	
	sklad();
	//example1();
	//example2();
	
	var sprite = new THREE.TextSprite({
	    textSize: .05,
	    texture: {
		text: 'Hello World!',
		fontFamily: 'Arial, Helvetica, sans-serif',
	    },
	    material: {color: 0xff00ff},
	});
	sprite.position.set(-1,1,1);
	scene.add(sprite);	
	
    }
    function sklad(){
	planeGeometry = new THREE.PlaneGeometry(15, 5);
	planeMaterial = new THREE.MeshBasicMaterial({color: 0xEdEdEd});
	//planeMaterial = new THREE.MeshLambertMaterial({color: 0xffffff});
	plane = new THREE.Mesh(planeGeometry, planeMaterial);
	plane.receiveShadow = true;//принимать тени
	plane.rotation.x = -0.5 * Math.PI;//поворот в горизонталь
	plane.position.x = 6;
	plane.position.y = 0;
	plane.position.z = 1;
	scene.add(plane);
	
	camera.position.x = 0.4;
	camera.position.y = 0.8;
	camera.position.z = 4;
	camera.lookAt(scene.position);
	//camera.lookAt(new THREE.Vector3(1.25, 0.8, 1));
	scene.translateX(-1);
	scene.translateY(-0.5);
	scene.translateZ(0);

	var ambientLight = new THREE.AmbientLight(0x0c0c0c);
	//var ambientLight = new THREE.AmbientLight(0x333333);
	scene.add(ambientLight);

	//var spotLight = new THREE.DirectionalLight(0xEEEEEE);
	var spotLight = new THREE.SpotLight(0xffffff);
	spotLight.position.set(6, 6, 3);
	spotLight.castShadow = true;//от этого источника света будут тени объектов
	scene.add(spotLight);
	
	var spotLight = new THREE.SpotLight(0xEEEEEE);
	spotLight.position.set(6, -3, 3);
	spotLight.castShadow = true;//от этого источника света будут тени объектов
	scene.add(spotLight);

	var spotLight1 = new THREE.SpotLight(0xEEEEEE);
	spotLight1.position.set(-6, 6, 3);
	spotLight1.castShadow = true;//от этого источника света будут тени объектов
	scene.add(spotLight1);

	var spotLight1 = new THREE.SpotLight(0xEEEEEE);
	spotLight1.position.set(-6, -6, 3);
	spotLight1.castShadow = true;//от этого источника света будут тени объектов
	scene.add(spotLight1);

	var spotLight = new THREE.SpotLight(0xffffff);
	spotLight.position.set(1, 1, -3);
	spotLight.castShadow = true;//от этого источника света будут тени объектов
	scene.add(spotLight);
	
	boxMaterials = [
	    //делаем каждую сторону своего цвета
	    new THREE.MeshBasicMaterial({color: 0xA8D7E9}), // правая сторона
	    new THREE.MeshBasicMaterial({color: 0xA8D7E9}), // левая сторона
	    new THREE.MeshBasicMaterial({color: 0xF7F7F7}), //верх
	    new THREE.MeshBasicMaterial({color: 0xF7F7F7}), // низ
	    new THREE.MeshBasicMaterial({color: 0x99C4D4}), // лицевая сторона
	    new THREE.MeshBasicMaterial({color: 0x99C4D4}) // задняя сторона
	];
	//выводим 5 полок
	var rackGeometry = new THREE.BoxGeometry(2500 * scale, 70 * scale, 900 * scale);
	var rackMaterial = new THREE.MeshLambertMaterial({color: 0xB4B8BB, wireframe: false, opacity: 0.9, transparent: true});
	rack = new THREE.Mesh(rackGeometry, rackMaterial);
	rack.position.set(rack.geometry.parameters.width / 2, rack.geometry.parameters.height / 2 + 0.08, rack.geometry.parameters.depth / 2);
	scene.add(rack);
	rack = new THREE.Mesh(rackGeometry, rackMaterial);
	rack.position.set(rack.geometry.parameters.width / 2, rack.geometry.parameters.height / 2 + 0.08 + (rack.geometry.parameters.height + 0.305), rack.geometry.parameters.depth / 2);
	scene.add(rack);
	rack = new THREE.Mesh(rackGeometry, rackMaterial);
	rack.position.set(rack.geometry.parameters.width / 2, rack.geometry.parameters.height / 2 + 0.08 + (rack.geometry.parameters.height + 0.305) * 2, rack.geometry.parameters.depth / 2);
	scene.add(rack);
	rack = new THREE.Mesh(rackGeometry, rackMaterial);
	rack.position.set(rack.geometry.parameters.width / 2, rack.geometry.parameters.height / 2 + 0.08 + (rack.geometry.parameters.height + 0.305) * 3, rack.geometry.parameters.depth / 2);
	scene.add(rack);
	rack = new THREE.Mesh(rackGeometry, rackMaterial);
	rack.position.set(rack.geometry.parameters.width / 2, rack.geometry.parameters.height / 2 + 0.08 + (rack.geometry.parameters.height + 0.305) * 4, rack.geometry.parameters.depth / 2);
	scene.add(rack);
	//выводин 4 стойки
	var standGeometry = new THREE.BoxGeometry(20 * scale, 1700 * scale, 50 * scale);
	var standMaterial = new THREE.MeshLambertMaterial({color: 0x0094FF, wireframe: false, opacity: 0.9, transparent: true});
	stand = new THREE.Mesh(standGeometry, standMaterial);
	stand.position.set(-stand.geometry.parameters.width / 2, stand.geometry.parameters.height / 2, stand.geometry.parameters.depth / 2);
	scene.add(stand);
	stand = new THREE.Mesh(standGeometry, standMaterial);
	stand.position.set(-stand.geometry.parameters.width / 2, stand.geometry.parameters.height / 2, stand.geometry.parameters.depth / 2 + 0.9 - stand.geometry.parameters.depth);
	scene.add(stand);
	stand = new THREE.Mesh(standGeometry, standMaterial);
	stand.position.set(stand.geometry.parameters.width / 1 + 2.5 - stand.geometry.parameters.width, stand.geometry.parameters.height / 2, stand.geometry.parameters.depth / 2);
	scene.add(stand);
	stand = new THREE.Mesh(standGeometry, standMaterial);
	stand.position.set(stand.geometry.parameters.width / 1 + 2.5 - stand.geometry.parameters.width, stand.geometry.parameters.height / 2, stand.geometry.parameters.depth / 2 + 0.9 - stand.geometry.parameters.depth);
	scene.add(stand);
	
	//вариант 1 - занимает 22*3=66 см
	boxX = 220 * scale; boxY = 294 * scale; boxZ = 266 * scale;
	startX = 0; startY = 150 * scale; startZ = 0;
	var boxGeometry = new THREE.BoxGeometry(boxX, boxY, boxZ);
	boxMaterial = new THREE.MeshLambertMaterial({color: 0xFFFF00, wireframe: true});
	box = new THREE.Mesh(boxGeometry, boxMaterial);
	box.position.set(box.geometry.parameters.width / 2, box.geometry.parameters.height / 2, box.geometry.parameters.depth / 2);
	box.translateX(startX);
	box.translateY(startY);
	box.translateZ(startZ);
	scene.add(box);
	scene.add(box.clone().translateX(startX + boxX)); 
	scene.add(box.clone().translateX(startX + boxX*2)); 
	scene.add(box.clone().translateZ(startZ + boxZ));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ));
	scene.add(box.clone().translateX(startX + boxX*2).translateZ(startZ + boxZ));
	scene.add(box.clone().translateZ(startZ + boxZ*2));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ*2));
	scene.add(box.clone().translateX(startX + boxX*2).translateZ(startZ + boxZ*2));
	scene.add(box.clone().translateZ(startZ + boxZ*3));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ*3));
	scene.add(box.clone().translateX(startX + boxX*2).translateZ(startZ + boxZ*3));
	
	//вариант 2 - занимает 29,4*3=88,2 см
	boxX = 294 * scale; boxY = 266 * scale; boxZ = 220 * scale;
	startX = 0; startY = 525 * scale; startZ = 0;
	var boxGeometry = new THREE.BoxGeometry(boxX, boxY, boxZ);
	boxMaterial = new THREE.MeshLambertMaterial({color: 0x7F0000, wireframe: true});
	box = new THREE.Mesh(boxGeometry, boxMaterial);
	box.position.set(box.geometry.parameters.width / 2, box.geometry.parameters.height / 2, box.geometry.parameters.depth / 2);
	box.translateX(startX);
	box.translateY(startY);
	box.translateZ(startZ);
	scene.add(box);
	scene.add(box.clone().translateX(startX + boxX)); 
	scene.add(box.clone().translateX(startX + boxX*2)); 
	scene.add(box.clone().translateZ(startZ + boxZ));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ));
	scene.add(box.clone().translateX(startX + boxX*2).translateZ(startZ + boxZ));
	scene.add(box.clone().translateZ(startZ + boxZ*2));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ*2));
	scene.add(box.clone().translateX(startX + boxX*2).translateZ(startZ + boxZ*2));
	scene.add(box.clone().translateZ(startZ + boxZ*3));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ*3));
	scene.add(box.clone().translateX(startX + boxX*2).translateZ(startZ + boxZ*3));
	
	//вариант 3 - занимает 26,6*3=79,8 см
	boxX = 266 * scale;
	boxY = 294 * scale;
	boxZ = 220 * scale;
	startX = 0;
	startY = 900 * scale;
	startZ = 0;
	var boxGeometry = new THREE.BoxGeometry(boxX, boxY, boxZ);
	boxMaterial = new THREE.MeshLambertMaterial({color: 0x0026FF, wireframe: true});
	box = new THREE.Mesh(boxGeometry, boxMaterial);
	box.position.set(box.geometry.parameters.width / 2, box.geometry.parameters.height / 2, box.geometry.parameters.depth / 2);
	box.translateX(startX);
	box.translateY(startY);
	box.translateZ(startZ);
	scene.add(box);
	scene.add(box.clone().translateX(startX + boxX));
	scene.add(box.clone().translateX(startX + boxX * 2));
	scene.add(box.clone().translateZ(startZ + boxZ));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ));
	scene.add(box.clone().translateX(startX + boxX * 2).translateZ(startZ + boxZ));
	scene.add(box.clone().translateZ(startZ + boxZ * 2));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ * 2));
	scene.add(box.clone().translateX(startX + boxX * 2).translateZ(startZ + boxZ * 2));
	scene.add(box.clone().translateZ(startZ + boxZ * 3));
	scene.add(box.clone().translateX(startX + boxX).translateZ(startZ + boxZ * 3));
	scene.add(box.clone().translateX(startX + boxX * 2).translateZ(startZ + boxZ * 3));

	//вариант 4 - занимает 29,4*2+26.6=79,8 см
	boxX = 294 * scale;
	boxY = 266 * scale;
	boxZ = 220 * scale;
	startX = 0;
	startY = 1275 * scale;
	startZ = 0;
	var boxGeometry = new THREE.BoxGeometry(boxX, boxY, boxZ);
	boxMaterial = new THREE.MeshLambertMaterial({color: 0x7F0000, wireframe: true});
	box = new THREE.Mesh(boxGeometry, boxMaterial);
	box.position.set(box.geometry.parameters.width / 2, box.geometry.parameters.height / 2, box.geometry.parameters.depth / 2);
	box.translateX(startX);
	box.translateY(startY);
	box.translateZ(startZ);
	
	boxMaterial2 = new THREE.MeshLambertMaterial({color: 0x0026FF, wireframe: true});
	box2 = new THREE.Mesh(new THREE.BoxGeometry(boxY, boxX, boxZ), boxMaterial2);
	box2.position.set(box2.geometry.parameters.width / 2, box2.geometry.parameters.height / 2, box2.geometry.parameters.depth / 2);
	box2.translateX(startX);
	box2.translateY(startY);
	box2.translateZ(startZ);

	boxMaterial3 = new THREE.MeshLambertMaterial({color: 0xFFFF00, wireframe: true});
	box3 = new THREE.Mesh(new THREE.BoxGeometry(boxZ, boxX, boxY), boxMaterial3);
	box3.position.set(box3.geometry.parameters.width / 2, box3.geometry.parameters.height / 2, box3.geometry.parameters.depth / 2);
	box3.translateX(startX);
	box3.translateY(startY);
	box3.translateZ(startZ);
	
	scene.add(box);
	scene.add(box2.translateX(startX + boxX));
	scene.add(box3.translateX(startX + boxX + boxY));


//	var text = new MeshText2D("RIGHT", {align: textAlign.right, font: '30px Arial', fillStyle: '#000000', antialias: true})
//	scene.add(text)
//   
    }
    
    function render() { 
	stats.update();

	//example1R();
	
	requestAnimationFrame( render );
	renderer.render( scene, camera );
    }
    function addControls(){
	// mouse & keyboard controls
	orbitControls = new THREE.OrbitControls(camera);
	//controls.autoRotate = true;
    }
    function addAxes(size){
	var axes = new THREE.AxisHelper(size);
	scene.add(axes);
    }
    function addStats() {
	stats = new Stats();
	stats.setMode(0);
	stats.domElement.style.position = 'absolute';
	stats.domElement.style.left = '5px';
	stats.domElement.style.top = '65px';
	container_stat.append(stats.domElement);
    }
    
    function example1R(){
	cube.rotation.x += controls.rotationSpeed;
	cube.rotation.y += controls.rotationSpeed;
	cube.rotation.z += controls.rotationSpeed;
	//cube.translateX(0.05);

	step += controls.bouncingSpeed;
	//console.log(controls.bouncingSpeed);
	sphere.position.x = 20 + (10 * (Math.cos(step)));
	sphere.position.y = 2 + (10 * Math.abs(Math.sin(step)));
    }
    function example1(){
	var planeGeometry = new THREE.PlaneGeometry(60, 20, 1, 1);
	//var planeMaterial = new THREE.MeshBasicMaterial({color: 0xcccccc});
	var planeMaterial = new THREE.MeshLambertMaterial({color: 0xffffff});
	var plane = new THREE.Mesh(planeGeometry, planeMaterial);
	plane.receiveShadow = true;//принимать тени
	plane.rotation.x = -0.5 * Math.PI;
	plane.position.x = 15;
	plane.position.y = 0;
	plane.position.z = 0;
	scene.add(plane);

	var cubeGeometry = new THREE.CubeGeometry(4, 4, 4);
	//var cubeMaterial = new THREE.MeshBasicMaterial({color: 0xff0000, wireframe: true});
	var cubeMaterial = new THREE.MeshLambertMaterial({color: 0xff0000});
	cube = new THREE.Mesh(cubeGeometry, cubeMaterial);
	cube.castShadow = true;//отбрасывать тень
	cube.position.x = -4;
	cube.position.y = 3;
	cube.position.z = 0;
	scene.add(cube);

	var sphereGeometry = new THREE.SphereGeometry(4, 20, 20);
	//var sphereMaterial = new THREE.MeshBasicMaterial({color: 0x7777ff, wireframe: true});
	var sphereMaterial = new THREE.MeshLambertMaterial({color: 0x7777ff});
	sphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
	sphere.position.x = 20;
	sphere.position.y = 0;
	sphere.position.z = 2;
	sphere.castShadow = true;//отбрасывать тень
	scene.add(sphere);

	camera.position.x = -30;
	camera.position.y = 40;
	camera.position.z = 30;
	camera.lookAt(scene.position);

	var ambientLight = new THREE.AmbientLight(0x0c0c0c);
	scene.add(ambientLight);

	var spotLight = new THREE.SpotLight(0xffffff);
	spotLight.position.set(-40, 60, -10);
	spotLight.castShadow = true;//от этого источника света будут тени объектов
	scene.add(spotLight);
	
	controls.rotationSpeed = 0.02;
	controls.bouncingSpeed = 0.03;
	
	var gui = new dat.GUI();
	gui.domElement.style.margin = '65px 0';
	gui.add(controls, 'rotationSpeed', 0, 0.5);
	gui.add(controls, 'bouncingSpeed', 0, 0.5);
    }
    
    function example2(){
	planeGeometry = new THREE.PlaneGeometry(60, 40, 1, 1);
	var planeMaterial = new THREE.MeshLambertMaterial({color: 0xffffff});
	var plane = new THREE.Mesh(planeGeometry, planeMaterial);
	scene.add(plane);
	var ambientLight = new THREE.AmbientLight(0x0c0c0c);
	scene.add(ambientLight);
	var spotLight = new THREE.SpotLight(0xffffff);
	scene.add(spotLight);	

	camera.position.x = -30;
	camera.position.y = 40;
	camera.position.z = 30;
	camera.lookAt(scene.position);
	
	addCube();
	
	scene.fog = new THREE.FogExp2(0xffffff, 0.015);
    }
    function addCube(){
//	var cubeSize = Math.ceil((Math.random() * 3));
//	var cubeGeometry = new THREE.CubeGeometry(cubeSize, cubeSize, cubeSize);
//	var cubeMaterial = new THREE.MeshLambertMaterial(
//		{color: Math.random() * 0xffffff});
//	cube = new THREE.Mesh(cubeGeometry, cubeMaterial);
//	cube.castShadow = true;
//	cube.name = "cube-" + scene.children.length;
//	cube.position.x = -30 + Math.round((Math.random() * planeGeometry.width));
//	cube.position.y = Math.round((Math.random() * 5));
//	cube.position.z = -20 + Math.round((Math.random() * planeGeometry.height));
//	scene.add(cube);
//	this.numberOfObjects = scene.children.length;	
	var vertices = [
	    new THREE.Vector3(1, 3, 1),
	    new THREE.Vector3(1, 3, -1),
	    new THREE.Vector3(1, -1, 1),
	    new THREE.Vector3(1, -1, -1),
	    new THREE.Vector3(-1, 3, -1),
	    new THREE.Vector3(-1, 3, 1),
	    new THREE.Vector3(-1, -1, -1),
	    new THREE.Vector3(-1, -1, 1)
	];
	var faces = [
	    new THREE.Face3(0, 2, 1),
	    new THREE.Face3(2, 3, 1),
	    new THREE.Face3(4, 6, 5),
	    new THREE.Face3(6, 7, 5),
	    new THREE.Face3(4, 5, 1),
	    new THREE.Face3(5, 0, 1),
	    new THREE.Face3(7, 6, 2),
	    new THREE.Face3(6, 3, 2),
	    new THREE.Face3(5, 7, 0),
	    new THREE.Face3(7, 2, 0),
	    new THREE.Face3(1, 3, 4),
	    new THREE.Face3(3, 6, 4),
	];
	var geom = new THREE.Geometry();
	geom.vertices = vertices;
	geom.faces = faces;
	//geom.computeCentroids();
	geom.mergeVertices();    
	var cubeMaterial = new THREE.MeshLambertMaterial({color: Math.random() * 0xffffff});
	cube = new THREE.Mesh(geom, cubeMaterial);
	scene.add(cube);
    }
});