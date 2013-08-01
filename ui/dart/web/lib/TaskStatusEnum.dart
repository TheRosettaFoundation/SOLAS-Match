library SolasMatchDart;

class TaskStatusEnum
{
  static const WAITING_FOR_PREREQUISITES = const TaskStatusEnum._(1);
  static const PENDING_CLAIM = const TaskStatusEnum._(2);
  static const IN_PROGRESS = const TaskStatusEnum._(3);
  static const COMPLETE = const TaskStatusEnum._(4);
  
  static get values => [WAITING_FOR_PREREQUISITES, PENDING_CLAIM, IN_PROGRESS, COMPLETE];
  
  final int value;
  
  const TaskStatusEnum._(this.value);
}