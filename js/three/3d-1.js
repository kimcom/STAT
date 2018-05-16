$(function() {
    var container,scene,camera,render,cube;
    init();
    render();
    function init() { 
	container = document.getElementById( 'webGL-container' );
	camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, 1, 10000 );
	camera.position.z = 10;

	scene = new THREE.Scene();

	light = new THREE.DirectionalLight( 0xffffff );
	light.position.set( 0, 0, 1 ).normalize();
	scene.add( light );

	var loader = new THREE.JSONLoader();
	loader.load( "../../js/cube.json", createScene1 );

	renderer = new THREE.WebGLRenderer( { antialias: true } );
	renderer.setPixelRatio( window.devicePixelRatio );
	renderer.setSize( window.innerWidth, window.innerHeight );
	container.appendChild( renderer.domElement );
				
    }
	
    function createScene1( geometry, materials ) {
	    cube = new THREE.Mesh( geometry, new THREE.MeshFaceMaterial( materials ) );
	    scene.add( cube );
    }
			
    function render() { 
	    requestAnimationFrame( render );
	    cube.rotation.y +=0.01;
	    renderer.render( scene, camera );
    }
});