library SolasMatchDart;

class TaskTypeEnum
{
  static const SEGMENTATION = const TaskTypeEnum._(1);
  static const TRANSLATION = const TaskTypeEnum._(2);
  static const PROOFREADING = const TaskTypeEnum._(3);
  static const DESEGMENTATION = const TaskTypeEnum._(4);
  
  static get values => [SEGMENTATION, TRANSLATION, PROOFREADING, DESEGMENTATION];
  
  final int value;
  
  const TaskTypeEnum._(this.value);
}