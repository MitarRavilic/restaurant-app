var restaurantApp = angular.module('restaurantApp',['ngMaterial', 'ngMessages', 'angularjs-datetime-picker', 'angular-momentjs'])
.config(function($interpolateProvider, $mdThemingProvider, $momentProvider){
    $momentProvider
      .asyncLoading(true)
      .scriptUrl('http://localhost/pivt/resources/libs/moment/min/moment.min.js');
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    $mdThemingProvider.theme('docs-dark', 'default')
      .primaryPalette('yellow')
      .dark();
})
.directive('stringToNumber', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, ngModel) {
        ngModel.$parsers.push(function(value) {
          return '' + value;
        });
        ngModel.$formatters.push(function(value) {
          return parseFloat(value);
        });
      }
    };
});

restaurantApp.controller('CartController', ['$scope', '$http', '$window', function($scope, $http, $window, $moment) {
    $scope.BASE = 'http://localhost/pivt/';
    $scope.cartItems = {};
    $scope.totalPrice = 0;
    $scope.status = false;

    $scope.logout = function(){
        $scope.status = true;
    }

    $scope.login = function(message){
        if(message == 'Successful login'){
            $scope.status = true;
        }
    }
  
    // Cart 
    $scope.initData = function(){
        $http({
            method : 'GET',
            url : $scope.BASE + 'api/cart'
        }).then(function(response) {
            $scope.cartItems = response.data.cartItems;
            for(var i = 0; i < $scope.cartItems.length; i++){
                $scope.totalPrice += parseInt($scope.cartItems[i].price) * parseInt($scope.cartItems[i].amount);
            }
        });
      };
    

    $scope.checkoutCart = function(id){
        $window.location.href = $scope.BASE + 'cart/checkout/order';
    };

    $scope.processOrder = function(personal_preference, delivery_address, delivery_time){
        delivery_time = moment().format("YYYY-MM-DD") + ' '  + moment(delivery_time).format("HH:mm");
        
        $scope.orderDetails = "";
        $scope.orderDetails = {
            "personal_preference": personal_preference,
            "delivery_address": delivery_address,
            "delivery_time": delivery_time
        }
        $scope.orderDetails = JSON.stringify($scope.orderDetails);

        $scope.items = "";
        for(var i = 0; i < $scope.cartItems.length; i++){
            $scope.items += JSON.stringify($scope.cartItems[i]);
        }

        $http({
            method : 'POST',
            url : $scope.BASE + 'order/processOrder',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'orderDetails': JSON.stringify($scope.orderDetails),'orderItems': JSON.stringify($scope.items)}
        }).then(function(response) {
            if(response.data){
                console.log(response.data);
            }
        });
    };

    $scope.initData();
}]);

restaurantApp.controller('AdminManageUserController', ['$scope', '$http', '$window', function($scope, $http, $window) {

    $scope.BASE = 'http://localhost/pivt/';
    $scope.users = {};

    $scope.initUsers = function(){
        $http({
            method : 'GET',
            url : $scope.BASE + 'api/users'
        }).then(function(response) {
            if(response.data){
                $scope.users = {};
                $scope.users = response.data.users;
            }
        });
    };

    $scope.createUser = function(email, first_name, last_name, address1, address2, address3, username, password, confirm_password, role, phone){
        address2 == undefined ? address2 = 'empty' : address2
        address3 == undefined ? address3 = 'empty' : address3

        $scope.user = {
            "email": email,
            "first_name": first_name,
            "last_name": last_name,
            "address1": address1,
            "address2": address2,
            "address3": address3,
            "username": username,
            "password1": password,
            "password2": confirm_password,
            "role": role,
            "phone": phone
        }

        $scope.user = JSON.stringify($scope.user);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/user/register/create',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'user': JSON.stringify($scope.user)}
        }).then(function(response) {
            console.log(response.data);
        });
    };

    $scope.updateUser = function(id, email, first_name, last_name, address1, address2, address3, username, password, confirm_password, role, phone){
        address2 === null ? address2 = 'empty' : address2
        address3 === null ? address3 = 'empty' : address3

        console.log(address2);
        console.log(address3);

        $scope.user = {
            "user_id": id,
            "email": email,
            "first_name": first_name,
            "last_name": last_name,
            "address1": address1,
            "address2": address2,
            "address3": address3,
            "username": username,
            "password1": password,
            "password2": confirm_password,
            "role": role,
            "phone": phone
        }

        $scope.user = JSON.stringify($scope.user);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/user/register/update',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'user': JSON.stringify($scope.user)}
        }).then(function(response) {
            console.log(response.data);
        });
    };

    $scope.deleteUser = function(id){
        $http({
            method : 'GET',
            url : $scope.BASE + 'admin/dashboard/user/register/delete/' + id
        }).then(function(response) {
            if(response.data){
                alert(response.data);
            }
        });
    };

    $scope.initUsers();
}]);

restaurantApp.controller('AdminManageItemController', ['$scope', '$http', '$window', 'filterFilter', function($scope, $http, $window, filterFilter) {
    $scope.BASE = 'http://localhost/pivt/';
    $scope.itemsMerged = {};
    $scope.ingredients = [];
    $scope.selection = [];

    $scope.selectedIngredients = function selectedIngredients() {
        return filterFilter($scope.ingredients, { selected: true });
      };
    
      $scope.$watch('ingredients|filter:{selected:true}', function (nv) {
        $scope.selection = nv.map(function (ingredient) {
          return ingredient;
        });
      }, true);

    $scope.initData = function(){
        $http({
            method : 'GET',
            url : $scope.BASE + 'api/items'
        }).then(function(response) {
            $scope.itemsMerged = {};
            if(response.data.items){
                $scope.itemsMerged = response.data.items;
            }
        });

        $http({
            method : 'GET',
            url : $scope.BASE + 'api/ingredients/'
        }).then(function(response) {
            $scope.ingredients = {};
            if(response.data.ingredients){
                $scope.ingredients = response.data.ingredients;
            }
        });
    };

    $scope.updateItem = function(item_id, title, description, mass, calorie_count, 
        price, cat_title){
        $scope.item = {
            "item_id": item_id,
            "title": title,
            "description": description,
            "mass": mass,
            "calorie_count": calorie_count,
            "price": price,
            "category_title": cat_title,
        }

        $scope.item = JSON.stringify($scope.item);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/item/register/update',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'item': JSON.stringify($scope.item)}
        }).then(function(response) {
            console.log(response.data);
        });
    };

    $scope.deleteItem = function(id){
        $http({
            method : 'GET',
            url : $scope.BASE + 'admin/dashboard/item/register/delete/' + id
        }).then(function(response) {
            if(response.data){
                console.log(response.data);
            }
        });
    };

    $scope.createItem = function(title, description, mass, calorie_count, 
        price, cat_title){

        $scope.item = {
            "title": title,
            "description": description,
            "mass": mass,
            "calorie_count": calorie_count,
            "price": price,
            "category_title": cat_title,
        }

        $scope.ingredientsPost = "";
        for(var i = 0; i < $scope.selection.length; i++){
            $scope.ingredientsPost += JSON.stringify($scope.selection[i]);
        }

        $scope.item = JSON.stringify($scope.item);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/item/register/create',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'item': JSON.stringify($scope.item), 'ingredients' : JSON.stringify($scope.ingredientsPost)}
        }).then(function(response) {
            // $window.href = $scope.BASE + "admin/dashboard/item/register/create";
            console.log(response.data);
        });
    };

    $scope.initData();
}]);

restaurantApp.controller('AdminManageCategoryController', ['$scope', '$http', '$window', function($scope, $http, $window){
    $scope.BASE = 'http://localhost/pivt/';
    $scope.categories = {};

    $scope.initCategories = function(){
        $http({
            method : 'GET',
            url : $scope.BASE + 'api/categories'
        }).then(function(response) {
            $scope.categories = {};
            if(response.data.categories){
                $scope.categories = response.data.categories;
            }
        });
    };

    $scope.updateCategory = function(category_id, title){
        $scope.category = {
            "category_id": category_id,
            "title": title
        };

        $scope.category = JSON.stringify($scope.category);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/category/register/update',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'category': JSON.stringify($scope.category)}
        }).then(function(response) {
            console.log(response.data);
        });
    };

    $scope.deleteCategory = function(id){
        $http({
            method : 'GET',
            url : $scope.BASE + 'admin/dashboard/category/register/delete/' + id
        }).then(function(response) {
            if(response.data){
                console.log(response.data);
            }
        });
    };

    $scope.createCategory = function(title){
        $scope.category = {
            "title": title
        }

        $scope.category = JSON.stringify($scope.category);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/category/register/create',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'category': JSON.stringify($scope.category)}
        }).then(function(response) {
            console.log(response.data);
        });
    };

    $scope.initCategories();
}]);

restaurantApp.controller('AdminManageIngredientController', ['$scope', '$http', '$window', function($scope, $http, $window){
    $scope.BASE = 'http://localhost/pivt/';
    $scope.ingredients = {};

    $scope.initIngredients = function(){
        $http({
            method : 'GET',
            url : $scope.BASE + 'api/ingredients'
        }).then(function(response) {
            $scope.ingredients = {};
            if(response.data.ingredients){
                $scope.ingredients = response.data.ingredients;
            }
        });
    };

    $scope.updateIngredient = function(ingredient_id, title, allergens){
        $scope.ingredient = {
            "ingredient_id": ingredient_id,
            "title": title,
            "allergens": allergens
        };

        $scope.ingredient = JSON.stringify($scope.ingredient);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/ingredient/register/update',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'ingredient': JSON.stringify($scope.ingredient)}
        }).then(function(response) {
            console.log(response.data);
        });
    };

    $scope.deleteIngredient = function(id){
        $http({
            method : 'GET',
            url : $scope.BASE + 'admin/dashboard/ingredient/register/delete/' + id
        }).then(function(response) {
            if(response.data){
                console.log(response.data);
            }
        });
    };

    $scope.createIngredient = function(title, allergens){
        $scope.ingredient = {
            "title": title,
            "allergens": allergens
        }

        $scope.ingredient = JSON.stringify($scope.ingredient);

        $http({
            method : 'POST',
            url : $scope.BASE + 'admin/dashboard/ingredient/register/create',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'ingredient': JSON.stringify($scope.ingredient)}
        }).then(function(response) {
            console.log(response.data);
        });
    };

    $scope.initIngredients();
}]);

restaurantApp.controller('DispatcherDashboardController', ['$scope', '$http', '$window', function($scope, $http, $window){
    $scope.BASE = 'http://localhost/pivt/';
    $scope.orders = {};

    $scope.initData = function() {
        $http({
            method : 'GET',
            url : $scope.BASE + 'api/orders'
        }).then(function(response) {
            $scope.orders = {};
            if(response.data){
                $scope.orders = response.data.orders;
                console.log($scope.orders);
            }
        });
    };

    $scope.approve = function(order_id, delivery_at){

        $scope.orderProcess = {
            "order_id": order_id,
            "is_accepted": 1,
            "delivery_at": delivery_at
        };

        $scope.orderProcess = JSON.stringify($scope.orderProcess);

        $http({
            method : 'POST',
            url : $scope.BASE + 'dispatcher/dashboard/processOrder',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function(obj) {
                var str = [];
                for(var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {'order_process': JSON.stringify($scope.orderProcess)}
        }).then(function(response) {
            if(response.data){
                console.log(response.data);
            }
        });;
    }

    $scope.decline = function(order_id){

    }
    
    $scope.initData();
}]);

restaurantApp.controller('LoginController', ['$scope', '$http', '$window' , function(){
    $scope.status = false;


}]);