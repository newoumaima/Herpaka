var app = angular.module('app', ['autocomplete','ngRoute','ngTouch','ngAnimate', 'ui.bootstrap','angular-cache']).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
});

app.run(['$rootScope', 'cacheService', '$location', function($rootScope,cacheService, $location) {

    $rootScope.switchTo = function(headed) {

        return $location.path(headed)

    };


$rootScope.cartAdd = function (prod) {

console.log('Added!');
//ToDo use ngFlash

cacheService.addToCart(prod);
};




    $rootScope.checkVariation = function(variation){

        if (variation >0)
        {
            return "redclass";
        }
        else {
            return "greenclass";
        }
    };
}]);


app.controller('MyCtrl', function($scope, ProductRetriever, providerFactory) {

    /*$scope.products = ProductRetriever.getproducts("...");
    $scope.products.then(function(data){
      $scope.products = data;
    });*/
    $scope.productsName = [];
    var getNames = function(tab) {
        var pn = []
        for (var i = 0; i < tab.length; i++) {
            if (pn.indexOf(tab[i].ProductName) == -1) {
                pn.push(tab[i].ProductName);
            }
        }
        /*console.log('array')
        console.log(pn)*/
        return pn;
    }

    $scope.pr = [];
    $scope.products = providerFactory.fetchProducts();
    $scope.products.then(function(resp) {
        $scope.pr = resp.data.data;
        $scope.products = ProductRetriever.getproducts("...", getNames($scope.pr), getNames($scope.pr));
        $scope.products.then(function(data) {
            $scope.products = getNames($scope.pr);
        });

    });

    $scope.getproducts = function() {
        return $scope.products;
    }

    $scope.doSomething = function(typedthings) {
        console.log("Do something like reload data with this: " + typedthings);
        $scope.newproducts = ProductRetriever.getproducts(typedthings, getNames($scope.pr), getNames($scope.pr));
        $scope.newproducts.then(function(data) {
            $scope.products = data;
            console.log("did something: "+ suggestion);
        });
    }

    $scope.doSomethingElse = function(suggestion) {
        console.log("Suggestion selected: " + suggestion);
    }

});
