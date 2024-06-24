require('./bootstrap');
import { AreaMeasure } from "cesium-extends";


async function initMap(){
  //// start setup CESIUM
  var extent = Cesium.Rectangle.fromDegrees(133.9147654553699, 33.95292549995615, 133.9147654553699, 34.87292549995615);
  Cesium.Camera.DEFAULT_VIEW_RECTANGLE = extent;
  Cesium.Camera.DEFAULT_VIEW_FACTOR = 0;
  Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJmNzg3YTg5ZS00MzZiLTRhZWYtOWM1OS1lOTc0N2Y0ZDhkYTAiLCJpZCI6MTY5ODcyLCJpYXQiOjE2OTYzOTY4NjZ9.t7uyczqwtg4JqZqN6eSuEsLQq0YsPPYdSqhrkmtrjLY';
  const viewer = await new Cesium.Viewer('cesiumContainer');
  window.viewer = viewer;


  const gl = document.createElement('canvas').getContext('webgl');
  const maxTextureSize = gl.getParameter(gl.MAX_TEXTURE_SIZE);
  console.log('Maximum Texture Size Supported:', maxTextureSize);
  //Maximum Texture Size Supported

  const areaMeasure = await new AreaMeasure(viewer);
  window.AreaMeasure = areaMeasure;
  areaMeasure.start();
}
initMap();