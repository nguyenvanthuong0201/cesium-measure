require('./bootstrap');
import { AreaMeasure } from "cesium-extends";
window.AreaMeasure = AreaMeasure;

//// start setup CESIUM
var extent = Cesium.Rectangle.fromDegrees(133.9147654553699, 33.95292549995615, 133.9147654553699, 34.87292549995615);
Cesium.Camera.DEFAULT_VIEW_RECTANGLE = extent;
Cesium.Camera.DEFAULT_VIEW_FACTOR = 0;
// Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJmNzg3YTg5ZS00MzZiLTRhZWYtOWM1OS1lOTc0N2Y0ZDhkYTAiLCJpZCI6MTY5ODcyLCJpYXQiOjE2OTYzOTY4NjZ9.t7uyczqwtg4JqZqN6eSuEsLQq0YsPPYdSqhrkmtrjLY';
Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJhMzdhNTE3Yy1hMzRjLTRkNTEtODk0Yy1kYzM3MmM1NTg3N2QiLCJpZCI6ODI1MDAsImlhdCI6MTY0NDg5NTc4Nn0.SZ41QKF9GvoHwr61DBmUXWyLHsraHk1tpU7Xv1E4sCw';

window.viewer = new Cesium.Viewer('cesiumContainer', {
  animation: false,
  sceneModePicker: false,
  scene3DOnly: true, // Chỉ sử dụng chế độ 3D
  // sceneMode: Cesium.SceneMode.SCENE3D,
  timeline: false,
  automaticallyTrackDataSourceClocks: false,
  //Hide the base layer picker
  baseLayerPicker: false,
  mapProjection: new Cesium.WebMercatorProjection(),
  requestRenderMode: true,
  maximumRenderTimeChange: Infinity,
  infoBox: false, // visible popup
  geocoder: false,
  showRenderLoopErrors: false,
  selectionIndicator: false,
  navigationHelpButton:false,
  // terrain: Cesium.Terrain.fromWorldTerrain(),
  homeButton: false,
});
// viewer.scene.globe.depthTestAgainstTerrain = false;
// viewer.scene.globe.translucency.frontFaceAlpha = 0.9;
// viewer.scene.globe.translucency.enabled = true;
