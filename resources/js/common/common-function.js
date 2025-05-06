export function commonFuntions() {
    function converToCurrencyFormat(d = "0") {
        return parseFloat(d)
            .toLocaleString("en-US", {
                style: "decimal",
                maximumFractionDigits: 2,
                minimumFractionDigits: 2,
            })
            .toString();
    }

    function convertNumberWithoutDigits(d = "0") {
        return parseInt(d)
            .toLocaleString("en-US", {
                maximumFractionDigits: 0,
                minimumFractionDigits: 0,
            })
            .toString();
    }

    function isNumber(evt) {
        evt = evt ? evt : window.event;
        var charCode = evt.which ? evt.which : evt.keyCode;
        if (
            charCode > 31 &&
            (charCode < 48 || charCode > 57) &&
            charCode !== 46
        ) {
            evt.preventDefault();
        } else {
            var count = 0;
            if (evt.target.value) {
                var valInputs = evt.target.value.split("").filter((d) => {
                    return d == evt.key && evt.keyCode == 46;
                });
                count += valInputs.length;
            }
            if (!count) {
                return true;
            } else {
                evt.preventDefault();
            }
        }
    }
    return {
        converToCurrencyFormat,
        convertNumberWithoutDigits,
        isNumber,
    };
}
