import 'package:wakDak/data/model/orderLiveTrackingModel.dart';
import 'package:wakDak/data/model/orderModel.dart';
import 'package:wakDak/data/repositories/order/orderRemoteDataSource.dart';
import 'package:wakDak/utils/apiMessageAndCodeException.dart';

class OrderRepository {
  static final OrderRepository _orderRepository = OrderRepository._internal();
  late OrderRemoteDataSource _orderRemoteDataSource;

  factory OrderRepository() {
    _orderRepository._orderRemoteDataSource = OrderRemoteDataSource();
    return _orderRepository;
  }
  OrderRepository._internal();

  // To getOrder
  Future<OrderModel> getOrderData(String? status, String? orderId, String? reason) async {
    try {
      OrderModel result = await _orderRemoteDataSource.getOrder(status: status, orderId: orderId, reason: reason);
      return result;
    } on ApiMessageAndCodeException catch (e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      throw ApiMessageAndCodeException(
          errorMessage: apiMessageAndCodeException.errorMessage.toString(), errorStatusCode: apiMessageAndCodeException.errorStatusCode.toString());
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  // To getOrderLiveTracking
  Future<OrderLiveTrackingModel> getOrderLiveTrackingData(String? orderId) async {
    try {
      OrderLiveTrackingModel result = await _orderRemoteDataSource.getOrderLiveTracing(orderId: orderId);
      return result;
    } on ApiMessageAndCodeException catch (e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      throw ApiMessageAndCodeException(
          errorMessage: apiMessageAndCodeException.errorMessage.toString(), errorStatusCode: apiMessageAndCodeException.errorStatusCode.toString());
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }

  // To reOrder
  Future<Map<String, dynamic>> reOrderData({String? orderId}) async {
    try {
      final result = await _orderRemoteDataSource.reOrder(orderId: orderId);
      return Map.from(result); //
    } on ApiMessageAndCodeException catch (e) {
      ApiMessageAndCodeException apiMessageAndCodeException = e;
      throw ApiMessageAndCodeException(
          errorMessage: apiMessageAndCodeException.errorMessage.toString(), errorStatusCode: apiMessageAndCodeException.errorStatusCode.toString());
    } catch (e) {
      throw ApiMessageAndCodeException(errorMessage: e.toString());
    }
  }
}
