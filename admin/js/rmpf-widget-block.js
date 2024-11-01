var el = wp.element.createElement,
  components = wp.components,
  blockControls = wp.blockEditor.BlockControls,
  inspectorControls = wp.blockEditor.InspectorControls;
var api_url = rmpf_base_url;
wp.blocks.registerBlockType("wp-roadmap/rmpf-widget", {
  title: "Roadmap Widget",
  description: "A custom block for showing roadmap and feedback",
  category: "rmpf-widget-blocks",
  icon: el(
    "svg",
    { width: "24", height: "24", viewBox: "0 0 512 512" },
    el("path", {
      style: { fill: "#482ea8" },
      d: "M507.31 84.69L464 41.37c-6-6-14.14-9.37-22.63-9.37H288V16c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v16H56c-13.25 0-24 10.75-24 24v80c0 13.25 10.75 24 24 24h385.37c8.49 0 16.62-3.37 22.63-9.37l43.31-43.31c6.25-6.26 6.25-16.38 0-22.63zM224 496c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V384h-64v112zm232-272H288v-32h-64v32H70.63c-8.49 0-16.62 3.37-22.63 9.37L4.69 276.69c-6.25 6.25-6.25 16.38 0 22.63L48 342.63c6 6 14.14 9.37 22.63 9.37H456c13.25 0 24-10.75 24-24v-80c0-13.25-10.75-24-24-24z",
    })
  ),
  attributes: {
    short_code: {
      type: "string",
      default: "[rmpf_roadmap_widget]",
    },
    roadmap: {
      type: Object,
    },
  },
  edit: function (props) {
    if (!props.attributes.roadmap) {
      wp.apiFetch({
        url:
          rmpf_base_url +
          "/index.php/wp-json/wp/v2/block-renderer/feedback/api/v1/test/render_shortcode",
      }).then((data) => {
        props.setAttributes({
          roadmap: data.data,
        });
      });
    }
    return wp.element.createElement(
      "div", // tag type
      {
        dangerouslySetInnerHTML: {
          __html: props.attributes.roadmap,
        },
      } // attributes
    );
  },
  save: function (props) {
    return wp.element.createElement(
      "div", // tag type
      {
        dangerouslySetInnerHTML: {
          __html: props.attributes.roadmap,
        },
      } // attributes
    );
  },
});
