"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.PhoneType = exports.EmailType = exports.Vertical = void 0;
var Vertical;
(function (Vertical) {
    Vertical["LOCAL_SERVICES"] = "local_services";
    Vertical["B2B_SAAS"] = "b2b_saas";
    Vertical["ECOMMERCE"] = "ecommerce";
    Vertical["HEALTHCARE"] = "healthcare";
    Vertical["REAL_ESTATE"] = "real_estate";
    Vertical["OTHER"] = "other";
})(Vertical || (exports.Vertical = Vertical = {}));
var EmailType;
(function (EmailType) {
    EmailType["GENERIC"] = "generic";
    EmailType["ROLE"] = "role";
    EmailType["PERSONAL"] = "personal";
})(EmailType || (exports.EmailType = EmailType = {}));
var PhoneType;
(function (PhoneType) {
    PhoneType["MAIN"] = "main";
    PhoneType["MOBILE"] = "mobile";
    PhoneType["FAX"] = "fax";
})(PhoneType || (exports.PhoneType = PhoneType = {}));
//# sourceMappingURL=canonical.js.map