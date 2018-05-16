$(function() {
    var container, scene, camera, renderer, controls, cube;
    container = document.getElementById('WebGL-output');
    //проверка поддержки WebGL
    if (Detector.webgl) {
	init();
	render();
    } else {
	var warning = Detector.getWebGLErrorMessage();
	container.appendChild(warning);
    }
    
    function init() { 
	var geometry, material;
	scene = new THREE.Scene();
	scene.background = new THREE.Color(0xEDEDED);

	camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
	camera.position.set(2, 2, 10);
	//renderer = new THREE.WebGLRenderer();
	//renderer = new THREE.WebGLRenderer({antialias: true});
	renderer = new THREE.WebGLRenderer({antialias: true,alpha: true});
	renderer.setPixelRatio(window.devicePixelRatio);
	renderer.setClearColor(0x000000, 1);
	renderer.setSize(window.innerWidth, window.innerHeight);
	container.appendChild(renderer.domElement);
	
	
//	geometry = new THREE.BoxGeometry(20, 3, 5);
//	material = new THREE.MeshBasicMaterial({color: 0xB2E4F7});
//	room = new THREE.Mesh(geometry, material);
	//scene.add(room);

	geometry = new THREE.BoxGeometry(2, 1, 1, 0.05, 0.05, 0.05);
	material = new THREE.MeshBasicMaterial({color: 0x99C4D5, wireframe: false, opacity: 0.5, transparent: true});
	cube = new THREE.Mesh(geometry, material);
	scene.add(cube);

	camera.lookAt(new THREE.Vector3(0, 0, 0));
	
	//создадим LineBasicMaterial синего цвета
	var material = new THREE.LineBasicMaterial({color: 0x0000ff});
	var geometry = new THREE.Geometry();
	geometry.vertices.push(	new THREE.Vector3(0, 5, 0),
				new THREE.Vector3(0, 0, 0),
				new THREE.Vector3(0, 0, 5),
				new THREE.Vector3(0, 0, 0),
				new THREE.Vector3(5, 0, 0));
	var line = new THREE.Line(geometry, material);
	scene.add(line);
	
	var lights = [];
	lights[ 0 ] = new THREE.PointLight(0xffffff, 1, 0);
	lights[ 1 ] = new THREE.PointLight(0xffffff, 1, 0);
	lights[ 2 ] = new THREE.PointLight(0xffffff, 1, 0);

	lights[ 0 ].position.set(0, 200, 0);
	lights[ 1 ].position.set(100, 200, 100);
	lights[ 2 ].position.set(-100, -200, -100);

	scene.add(lights[ 0 ]);
	scene.add(lights[ 1 ]);
	scene.add(lights[ 2 ]);
	
/*
	var loader = new THREE.JSONLoader();
	loader.load("../../js/cube.json", createScene1);

	var mesh = new THREE.Object3D();

	mesh.add(new THREE.LineSegments(
		new THREE.Geometry(),
		new THREE.LineBasicMaterial({
		    color: 0xffffff,
		    transparent: true,
		    opacity: 0.5
		})

		));

	mesh.add(new THREE.Mesh(
		new THREE.Geometry(),
		new THREE.MeshPhongMaterial({
		    color: 0x156289,
		    emissive: 0x072534,
		    side: THREE.DoubleSide,
		    flatShading: true
		})

		));
	scene.add(mesh);
*/	
	// mouse controls
	controls = new THREE.OrbitControls(camera);
	//controls.autoRotate = true;
    }
	
    function createScene1( geometry, materials ) {
	    cube = new THREE.Mesh( geometry, new THREE.MeshFaceMaterial( materials ) );
	    cube.position.set(3, 0, 0);
	    //cube.geometry.parameters.width = 3;
	    scene.add( cube );
    }
			
    function render() { 
	    requestAnimationFrame( render );
	    //cube.rotation.x += 0.02;
	    //cube.rotation.y += 0.02;
	    //cube.rotation.z += 0.02;
	    //mesh.rotation.x += 0.01;
	    controls.update();
	    renderer.render( scene, camera );
    }
});