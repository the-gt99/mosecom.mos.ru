<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>
<body>
<div class="row mb-2">
    <div class="col-md-6">
        <ul class="list-group" id="productsList"></ul>
    </div>
</div>


{{--<form class="row g-3">--}}
{{--    <div class="col-auto">--}}
{{--        <label for="productName" class="visually-hidden">Название продукта пиши)</label>--}}
{{--        <input type="password" class="form-control" id="productName" placeholder="Туть название продукта">--}}
{{--    </div>--}}
{{--    <div class="col-auto">--}}
{{--        <button type="submit" class="btn btn-primary mb-3">Найти</button>--}}
{{--    </div>--}}
{{--</form>--}}


<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf"
        crossorigin="anonymous"></script>
<script>

    const PageDrawer = {
        _addProduct(data) {
            console.log(data);
            $(`#productsList`).append(
                jQuery('<\li>', {
                    'class': 'list-group-item product',
                    'text': `${data.name}  ${data.cost} ${data.currency.name}`,
                    'id': data.id,
                    'data-cost': data.cost,
                    'data-currency-id': data.currency.id,
                    'data-currency-cost': data.currency.cost
                })
            );
        },
        _updateProductById(id, data) {
            $(`#${id}`)
                .text(`${data.name}  ${data.cost} ${data.currency.name}`)
                .attr('data-cost', data.cost);
        },
        updateProducts(products) {
            products.forEach(productInf => {
                if ($(`#${productInf.id}`).length > 0)
                    this._updateProductById(productInf.id, productInf);
                else
                    this._addProduct(productInf);
            });
        }
    };

    const Utils = {
        tryParseJSON(jsonString) {
            try {
                let o = JSON.parse(jsonString);

                if (o && typeof o === "object") {
                    return o;
                }
            } catch (e) {
            }

            return false;
        }
    };

    const SiteAPI = {
        async _get(url) {
            return new Promise((resolve) => {
                $.ajax({
                    type: "GET",
                    url: `${location.href}api/v1/${url}`,

                }).done(function (data) {
                    let response = Utils.tryParseJSON(data);

                    if (response)
                        resolve(response);
                    else
                        resolve([]);
                });
            });
        },
        async getProducts() {
            return await this._get(`products`)
        },
        async getCurrencies() {
            return await this._get(`currencies`)
        },
        async getCurrencyById(id) {
            return await this._get(`currencies/${id}`)
        },
    };

    const PageLogic = {
        async updateProducts() {
            let products = await SiteAPI.getProducts();
            PageDrawer.updateProducts(products);
        },
        _setListeners() {
            $(".product").click(async e => {
                let el = e.currentTarget;
                let cost = el.dataset.cost;
                console.log(el.dataset);
                let currencyId = el.dataset.currencyId;
                let currencyCost = el.dataset.currencyCost;

                let actualCurrency = await SiteAPI.getCurrencyById(currencyId);

                if(!actualCurrency)
                {
                    e.preventDefault();
                    alert("WTF?!");
                    return;
                }

                if(actualCurrency.cost != currencyCost)
                {
                    e.preventDefault();
                    await this.updateProducts();
                    return;
                }

                alert("Я не успеваю норм визуалку, так шо так)");

                let newCurrencyName = prompt('Введите id валюты:');
                let newCurrencyInf = await SiteAPI.getCurrencyById(newCurrencyName)

                if(newCurrencyInf)
                {
                    alert(cost / currencyCost * newCurrencyInf.cost);

                } else alert("Вы ввели чушь!!");
            });
        }
    };

    async function init() {

        await PageLogic.updateProducts();

        PageLogic._setListeners();
    }

    init();

    //todo
    (() => {
        let text = "Не забудь перед продой код сюда закинуть";
        console.log(`%c ${text} `, 'background: #222; color: #bada55');
    })();
</script>
</body>
</html>
