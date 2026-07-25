import 'package:flutter_test/flutter_test.dart';

import 'package:emp_platform_mobile/main.dart';

void main() {
  testWidgets('renders mobile login screen', (WidgetTester tester) async {
    await tester.pumpWidget(const EmpMobileApp());
    await tester.pump();

    expect(find.text('Enterprise Management Platform'), findsOneWidget);
    expect(find.text('Access Mobile Jobs'), findsOneWidget);
  });
}
