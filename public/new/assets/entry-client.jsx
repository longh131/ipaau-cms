import.meta.env = {"BASE_URL": "/", "DEV": true, "MODE": "development", "PROD": false, "SSR": false};import __vite__cjsImport0_react_jsxDevRuntime from "/node_modules/.vite/deps/react_jsx-dev-runtime.js?v=a2722ce2"; const jsxDEV = __vite__cjsImport0_react_jsxDevRuntime["jsxDEV"];
import "/src/index.scss";
import __vite__cjsImport2_react from "/node_modules/.vite/deps/react.js?v=a2722ce2"; const React = __vite__cjsImport2_react.__esModule ? __vite__cjsImport2_react.default : __vite__cjsImport2_react;
import __vite__cjsImport3_reactDom_client from "/node_modules/.vite/deps/react-dom_client.js?v=564a9500"; const createRoot = __vite__cjsImport3_reactDom_client["createRoot"]; const hydrateRoot = __vite__cjsImport3_reactDom_client["hydrateRoot"];
import { HelmetProvider } from "/node_modules/.vite/deps/react-helmet-async.js?v=c83af03c";
import App from "/src/App.jsx";
const container = document.getElementById("root");
if (!container) {
  throw new Error("Root element #root not found");
}
const app = /* @__PURE__ */ jsxDEV(React.StrictMode, { children: /* @__PURE__ */ jsxDEV(HelmetProvider, { children: /* @__PURE__ */ jsxDEV(App, {}, void 0, false, {
  fileName: "/Users/daniels/sites/ipa_fe/src/entry-client.jsx",
  lineNumber: 19,
  columnNumber: 7
}, this) }, void 0, false, {
  fileName: "/Users/daniels/sites/ipa_fe/src/entry-client.jsx",
  lineNumber: 18,
  columnNumber: 5
}, this) }, void 0, false, {
  fileName: "/Users/daniels/sites/ipa_fe/src/entry-client.jsx",
  lineNumber: 17,
  columnNumber: 1
}, this);
if (import.meta.env.DEV) {
  createRoot(container).render(app);
} else {
  hydrateRoot(container, app);
}

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJtYXBwaW5ncyI6IkFBa0JNO0FBbEJOLE9BQU87QUFDUCxPQUFPQSxXQUFXO0FBQ2xCLFNBQVNDLFlBQVlDLG1CQUFtQjtBQUN4QyxTQUFTQyxzQkFBc0I7QUFDL0IsT0FBT0MsU0FBUztBQUVoQixNQUFNQyxZQUFZQyxTQUFTQyxlQUFlLE1BQU07QUFDaEQsSUFBSSxDQUFDRixXQUFXO0FBQ2QsUUFBTSxJQUFJRyxNQUFNLDhCQUE4QjtBQUNoRDtBQU1BLE1BQU1DLE1BQ0osdUJBQUMsTUFBTSxZQUFOLEVBQ0MsaUNBQUMsa0JBQ0MsaUNBQUMsU0FBRDtBQUFBO0FBQUE7QUFBQTtBQUFBLE9BQUksS0FETjtBQUFBO0FBQUE7QUFBQTtBQUFBLE9BRUEsS0FIRjtBQUFBO0FBQUE7QUFBQTtBQUFBLE9BSUE7QUFHRixJQUFJQyxZQUFZQyxJQUFJQyxLQUFLO0FBQ3ZCWCxhQUFXSSxTQUFTLEVBQUVRLE9BQU9KLEdBQUc7QUFDbEMsT0FBTztBQUNMUCxjQUFZRyxXQUFXSSxHQUFHO0FBQzVCIiwibmFtZXMiOlsiUmVhY3QiLCJjcmVhdGVSb290IiwiaHlkcmF0ZVJvb3QiLCJIZWxtZXRQcm92aWRlciIsIkFwcCIsImNvbnRhaW5lciIsImRvY3VtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJFcnJvciIsImFwcCIsImltcG9ydCIsImVudiIsIkRFViIsInJlbmRlciJdLCJpZ25vcmVMaXN0IjpbXSwic291cmNlcyI6WyJlbnRyeS1jbGllbnQuanN4Il0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCAnLi9pbmRleC5zY3NzJ1xuaW1wb3J0IFJlYWN0IGZyb20gJ3JlYWN0J1xuaW1wb3J0IHsgY3JlYXRlUm9vdCwgaHlkcmF0ZVJvb3QgfSBmcm9tICdyZWFjdC1kb20vY2xpZW50J1xuaW1wb3J0IHsgSGVsbWV0UHJvdmlkZXIgfSBmcm9tICdyZWFjdC1oZWxtZXQtYXN5bmMnXG5pbXBvcnQgQXBwIGZyb20gJy4vQXBwJ1xuXG5jb25zdCBjb250YWluZXIgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncm9vdCcpXG5pZiAoIWNvbnRhaW5lcikge1xuICB0aHJvdyBuZXcgRXJyb3IoJ1Jvb3QgZWxlbWVudCAjcm9vdCBub3QgZm91bmQnKVxufVxuXG4vLyBNYXRjaCBlbnRyeS1zZXJ2ZXIuanN4OiBTdHJpY3RNb2RlID4gSGVsbWV0UHJvdmlkZXIgPiBBcHAuIEEgbWlzbWF0Y2hlZCB0cmVlIGJyZWFrc1xuLy8gaHlkcmF0aW9uOyBmYWlsZWQgaHlkcmF0aW9uIGNhbiBsZWF2ZSB0aGUgYXBwIHdpdGhvdXQgYSBwcm9wZXIgY2xpZW50IG1vdW50LCBzb1xuLy8gdXNlRWZmZWN0IGluIEFwcCBuZXZlciBydW5zLiBJbiBkZXYsIHJlbmRlciBmcmVzaCB3aXRoIGNyZWF0ZVJvb3QgaW5zdGVhZCBvZlxuLy8gaHlkcmF0aW5nIHNvIGxvY2FsIGRldmVsb3BtZW50IGRvZXMgbm90IGRlcGVuZCBvbiBhIHBlcmZlY3QgU1NSIG1hdGNoLlxuY29uc3QgYXBwID0gKFxuICA8UmVhY3QuU3RyaWN0TW9kZT5cbiAgICA8SGVsbWV0UHJvdmlkZXI+XG4gICAgICA8QXBwIC8+XG4gICAgPC9IZWxtZXRQcm92aWRlcj5cbiAgPC9SZWFjdC5TdHJpY3RNb2RlPlxuKVxuXG5pZiAoaW1wb3J0Lm1ldGEuZW52LkRFVikge1xuICBjcmVhdGVSb290KGNvbnRhaW5lcikucmVuZGVyKGFwcClcbn0gZWxzZSB7XG4gIGh5ZHJhdGVSb290KGNvbnRhaW5lciwgYXBwKVxufVxuIl0sImZpbGUiOiIvVXNlcnMvZGFuaWVscy9zaXRlcy9pcGFfZmUvc3JjL2VudHJ5LWNsaWVudC5qc3gifQ==