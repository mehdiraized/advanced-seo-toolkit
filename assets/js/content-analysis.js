/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/content-analysis.js":
/*!************************************!*\
  !*** ./src/js/content-analysis.js ***!
  \************************************/
/***/ (() => {

eval("function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }\nfunction _nonIterableRest() { throw new TypeError(\"Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\nfunction _unsupportedIterableToArray(r, a) { if (r) { if (\"string\" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return \"Object\" === t && r.constructor && (t = r.constructor.name), \"Map\" === t || \"Set\" === t ? Array.from(r) : \"Arguments\" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }\nfunction _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }\nfunction _iterableToArrayLimit(r, l) { var t = null == r ? null : \"undefined\" != typeof Symbol && r[Symbol.iterator] || r[\"@@iterator\"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t[\"return\"] && (u = t[\"return\"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }\nfunction _arrayWithHoles(r) { if (Array.isArray(r)) return r; }\nconsole.log(\"Content Analysis script loaded\");\n(function (wp) {\n  var registerPlugin = wp.plugins.registerPlugin;\n  var PluginSidebar = wp.editPost.PluginSidebar;\n  var _wp$components = wp.components,\n    PanelBody = _wp$components.PanelBody,\n    Button = _wp$components.Button,\n    Notice = _wp$components.Notice;\n  var withSelect = wp.data.withSelect;\n  var _wp$element = wp.element,\n    Fragment = _wp$element.Fragment,\n    useState = _wp$element.useState;\n  var ContentAnalysis = function ContentAnalysis(props) {\n    var _useState = useState([]),\n      _useState2 = _slicedToArray(_useState, 2),\n      suggestions = _useState2[0],\n      setSuggestions = _useState2[1];\n    var analyzeContent = function analyzeContent() {\n      var content = props.content;\n      fetch(ASTContentAnalysis.ajax_url, {\n        method: \"POST\",\n        headers: {\n          \"Content-Type\": \"application/x-www-form-urlencoded; charset=UTF-8\"\n        },\n        body: new URLSearchParams({\n          action: \"ast_analyze_content\",\n          nonce: ASTContentAnalysis.nonce,\n          content: content\n        })\n      }).then(function (response) {\n        return response.json();\n      }).then(function (data) {\n        if (data.success) {\n          setSuggestions(data.data);\n        }\n      });\n    };\n    return /*#__PURE__*/React.createElement(PluginSidebar, {\n      name: \"ast-content-analysis-sidebar\",\n      title: \"Content Analysis\",\n      icon: \"admin-site\"\n    }, /*#__PURE__*/React.createElement(PanelBody, null, /*#__PURE__*/React.createElement(Button, {\n      isPrimary: true,\n      onClick: analyzeContent\n    }, \"Analyze Content\"), suggestions.length > 0 && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(\"h3\", null, \"SEO Suggestions\"), /*#__PURE__*/React.createElement(\"ul\", null, suggestions.map(function (suggestion, index) {\n      return /*#__PURE__*/React.createElement(\"li\", {\n        key: index\n      }, suggestion);\n    })))));\n  };\n  var mapSelectToProps = function mapSelectToProps(select) {\n    return {\n      content: select(\"core/editor\").getEditedPostContent()\n    };\n  };\n  var ContentAnalysisWithSelect = withSelect(mapSelectToProps)(ContentAnalysis);\n  registerPlugin(\"ast-content-analysis\", {\n    render: ContentAnalysisWithSelect\n  });\n})(window.wp);\n\n//# sourceURL=webpack://advanced-seo-toolkit/./src/js/content-analysis.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/js/content-analysis.js"]();
/******/ 	
/******/ })()
;