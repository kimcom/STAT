$(function() {
    var camera, scene, renderer;
    var mesh;

    init();
    animate();

    function init() {

	var AMOUNT = 6;
	var SIZE = 1 / AMOUNT;
	var ASPECT_RATIO = window.innerWidth / window.innerHeight;

	var cameras = [];

	for (var y = 0; y < AMOUNT; y++) {
	    for (var x = 0; x < AMOUNT; x++) {
		var subcamera = new THREE.PerspectiveCamera(40, ASPECT_RATIO, 0.1, 10);
		subcamera.bounds = new THREE.Vector4(x / AMOUNT, y / AMOUNT, SIZE, SIZE);
		subcamera.position.x = (x / AMOUNT) - 0.5;
		subcamera.position.y = 0.5 - (y / AMOUNT);
		subcamera.position.z = 1.5;
		subcamera.position.multiplyScalar(2);
		subcamera.lookAt(new THREE.Vector3());
		subcamera.updateMatrixWorld();
		cameras.push(subcamera);
	    }
	}

	camera = new THREE.ArrayCamera(cameras);
	camera.position.z = 3;

	scene = new THREE.Scene();

	scene.add(new THREE.AmbientLight(0x222244));

	var light = new THREE.DirectionalLight();
	light.position.set(0.5, 0.5, 1);
	light.castShadow = true;
	light.shadow.camera.zoom = 4; // tighter shadow map
	scene.add(light);

	var geometry = new THREE.PlaneBufferGeometry(100, 100);
	var material = new THREE.MeshPhongMaterial({color: 0x000066});

	var background = new THREE.Mesh(geometry, material);
	background.receiveShadow = true;
	background.position.set(0, 0, -1);
	scene.add(background);

	var geometry = new THREE.CylinderBufferGeometry(0.5, 0.5, 1, 32);
	var material = new THREE.MeshPhongMaterial({color: 0xff0000});

	mesh = new THREE.Mesh(geometry, material);
	mesh.castShadow = true;
	mesh.receiveShadow = true;
	scene.add(mesh);

	renderer = new THREE.WebGLRenderer();
	renderer.setPixelRatio(window.devicePixelRatio);
	renderer.setSize(window.innerWidth, window.innerHeight);
	renderer.shadowMap.enabled = true;
	document.body.appendChild(renderer.domElement);

	//

	window.addEventListener('resize', onWindowResize, false);
    }

    function onWindowResize() {

	camera.aspect = window.innerWidth / window.innerHeight;
	camera.updateProjectionMatrix();

	renderer.setSize(window.innerWidth, window.innerHeight);

    }

    function animate() {

	mesh.rotation.x += 0.005;
	mesh.rotation.z += 0.01;

	renderer.render(scene, camera);

	requestAnimationFrame(animate);

    }
});