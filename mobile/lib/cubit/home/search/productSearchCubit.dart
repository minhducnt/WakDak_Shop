import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:wakDak/data/model/sectionsModel.dart';
import 'package:wakDak/utils/api.dart';
import 'package:wakDak/utils/apiBodyParameterLabels.dart';
import 'package:wakDak/utils/apiMessageException.dart';

@immutable
abstract class ProductSearchState {}

class ProductSearchInitial extends ProductSearchState {}

class ProductSearchProgress extends ProductSearchState {}

class ProductSearchSuccess extends ProductSearchState {
  final List<ProductDetails> productSearchList;
  final int totalData;
  final bool hasMore;
  ProductSearchSuccess(this.productSearchList, this.totalData, this.hasMore);
}

class ProductSearchFailure extends ProductSearchState {
  final String errorMessage;
  ProductSearchFailure(this.errorMessage);
}

String? totalHasMore;

class ProductSearchCubit extends Cubit<ProductSearchState> {
  ProductSearchCubit() : super(ProductSearchInitial());
  Future<List<ProductDetails>> _fetchData({
    required String limit,
    String? offset,
    String? search,
    String? vegetarian,
    String? latitude,
    String? longitude,
    String? userId,
    String? cityId
  }) async {
    try {
      //
      // Body of post request
      final body = {
        limitKey: limit,
        offsetKey: offset ?? "",
        searchKey: search ?? "",
        filterByKey: filterByProductKey,
        vegetarianKey: vegetarian ?? "",
        latitudeKey: latitude ?? "",
        longitudeKey: longitude ?? "",
        userIdKey: userId ?? "",
        cityIdKey: cityId ?? "",
      };
      if (offset == null) {
        body.remove(offset);
      }
      final result = await Api.post(body: body, url: Api.getProductsUrl, token: true, errorCode: false);
      totalHasMore = result[totalKey].toString();
      return (result[dataKey] as List).map((e) => ProductDetails.fromJson(e)).toList();
    } catch (e) {
      throw ApiMessageException(errorMessage: e.toString());
    }
  }

  void fetchProductSearch(String limit, String? search, String vegetarian, String? latitude, String? longitude, String? userId,
      String? cityId) {
    emit(ProductSearchProgress());
    _fetchData(
            limit: limit,
            search: search,
            vegetarian: vegetarian,
            latitude: latitude,
            longitude: longitude,
            userId: userId,
            cityId: cityId
            )
        .then((value) {
      final List<ProductDetails> usersDetails = value;
      final total = int.parse(totalHasMore!);
      emit(ProductSearchSuccess(
        usersDetails,
        total,
        total > usersDetails.length,
      ));
    }).catchError((e) {
      emit(ProductSearchFailure(e.toString()));
    });
  }

  void fetchMoreProductSearchData(String limit, String? search, String vegetarian, String? latitude, String? longitude, String? userId,
      String? cityId) {
    _fetchData(
            limit: limit,
            offset: (state as ProductSearchSuccess).productSearchList.length.toString(),
            search: search,
            vegetarian: vegetarian,
            latitude: latitude,
            longitude: longitude,
            userId: userId,
            cityId: cityId
            )
        .then((value) {
      final oldState = (state as ProductSearchSuccess);
      final List<ProductDetails> usersDetails = value;
      final List<ProductDetails> updatedUserDetails = List.from(oldState.productSearchList);
      updatedUserDetails.addAll(usersDetails);
      emit(ProductSearchSuccess(updatedUserDetails, oldState.totalData, oldState.totalData > updatedUserDetails.length));
    }).catchError((e) {
      emit(ProductSearchFailure(e.toString()));
    });
  }

  bool hasMoreData() {
    if (state is ProductSearchSuccess) {
      return (state as ProductSearchSuccess).hasMore;
    } else {
      return false;
    }
  }
}
