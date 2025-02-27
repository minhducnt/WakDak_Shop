import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:wakDak/data/model/productRatingModel.dart';
import 'package:wakDak/utils/api.dart';
import 'package:wakDak/utils/apiBodyParameterLabels.dart';
import 'package:wakDak/utils/apiMessageAndCodeException.dart';

@immutable
abstract class GetProductRatingState {}

class GetProductRatingInitial extends GetProductRatingState {}

class GetProductRatingProgress extends GetProductRatingState {}

class GetProductRatingSuccess extends GetProductRatingState {
  final List<ProductRatingModel> productRatingList;
  final int totalData;
  final bool hasMore;
  GetProductRatingSuccess(this.productRatingList, this.totalData, this.hasMore);
}

class GetProductRatingFailure extends GetProductRatingState {
  final String errorMessage, errorStatusCode;
  GetProductRatingFailure(this.errorMessage, this.errorStatusCode);
}

String? totalHasMore;

class GetProductRatingCubit extends Cubit<GetProductRatingState> {
  GetProductRatingCubit() : super(GetProductRatingInitial());
  Future<List<ProductRatingModel>> _fetchData({
    required String limit,
    String? offset,
    String? productId,
  }) async {
    try {
      //body of post request
      final body = {limitKey: limit, offsetKey: offset ?? "", productIdKey: productId ?? ""};
      if (offset == null) {
        body.remove(offset);
      }
      final result = await Api.post(body: body, url: Api.getProductRatingUrl, token: true, errorCode: true);
      totalHasMore = result[totalKey].toString();
      return (result[dataKey] as List).map((e) => ProductRatingModel.fromJson(e)).toList();
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  void fetchGetProductRating(String limit, String productId) {
    emit(GetProductRatingProgress());
    _fetchData(limit: limit, productId: productId).then((value) {
      final List<ProductRatingModel> usersDetails = value;
      final total = int.parse(totalHasMore!);
      emit(GetProductRatingSuccess(
        usersDetails,
        total,
        total > usersDetails.length,
      ));
    }).catchError((e) {
      ApiMessageAndCodeException ratingException = e;
      emit(GetProductRatingFailure(ratingException.errorMessage.toString(), ratingException.errorStatusCode.toString()));
    });
  }

  void fetchMoreGetProductRatingData(String limit, String productId) {
    _fetchData(limit: limit, offset: (state as GetProductRatingSuccess).productRatingList.length.toString(), productId: productId).then((value) {
      final oldState = (state as GetProductRatingSuccess);
      final List<ProductRatingModel> usersDetails = value;
      final List<ProductRatingModel> updatedUserDetails = List.from(oldState.productRatingList);
      updatedUserDetails.addAll(usersDetails);
      emit(GetProductRatingSuccess(updatedUserDetails, oldState.totalData, oldState.totalData > updatedUserDetails.length));
    }).catchError((e) {
      ApiMessageAndCodeException ratingException = e;
      emit(GetProductRatingFailure(ratingException.errorMessage.toString(), ratingException.errorStatusCode.toString()));
    });
  }

  bool hasMoreData() {
    if (state is GetProductRatingSuccess) {
      return (state as GetProductRatingSuccess).hasMore;
    } else {
      return false;
    }
  }
}
