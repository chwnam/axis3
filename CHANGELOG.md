# CHANGELOG

## 0.0.9
2020년 02월 25일
- CustomPostAdminInitiator 키워드 지원 추가 (a25472d9)
  - KEY_ACTION_RESTRICT_MANAGE_POSTS
  - KEY_FILTER_ENTER_TITLE_HERE
  - KEY_FILTER_THE_EDITOR_CONTENT
- 한 화면에서 클래식 에디터 위젯을 여러 번 띄울 수 있도록 수정 (d4652624)
- RolesCapsModel 기능 수정 (2cb0111f)
- CustomPostModel::getPrimitiveCapabilities(d7fe3ae2) 메소드 추가
- humanReadableSize() 함수 제거 (5bf12ab2)


## 0.0.8
2020년 02월 21일
- 자잘한 오류 및 기능 수정 
  - action_current_screen() 메소드 시그니쳐 조정 (98ad2e6b)
  - CheckboxRadioWidget 기능 수정. (39da4f55)
  - fetchElement() 메소드 추가. (19c4c64f)
  - beforeSave, afterSave 메소드의 파라미터 힌트 제거. (4e171d11)
  - retrieve() 메소드 single=false 일때 warning 나오는 것 수정. (8ec6688d)
  - MetaFieldModel::retrieve() 메소드 single=false 경우 수정. (25aee862)
  - splitArray() 함수 기능 수정. (26d2f64b)


## 0.0.7
2020년 02월 20일
- 구조 변경. 아래 MenuPageView, SubMenuPageView 의 메소드는 스태틱 메소드화 되었습니다. (f636ac6f)
  - getPageTitle()
  - getMenuTitle()
  - getCapability()
  - getMenuSlug()
  - getParentSlug()
  - 해당 메소드는 나타낼 메뉴 페이지를 대표하는 성격을 가졌습니다. 해당 처리를 통해 메뉴의 정보를 좀더 편리하게 참조할 수 있습니다.
- 모델 클래스가 'activationSetup', 'deactivationCleanup' 메소드를 가지고 있으면 각각 활성화, 비활성화때 호출되도록 수정. (d2f493f5) 


## 0.0.6
2020년 02월 18일
- MediaLibraryWidget, saveField 값이 'id'인 경우는 readonly 속성을 부여. (41be2c99)
- MediaLibraryWidget, 첨부물이 이미지가 아닐 경우 발생하던 에러 수정 (0f389044)
- MetaFieldModel, single 필드가 아닐 때 저장 로직 구현. (89078d3f)
- CustomPostAdminInitiator, beforeSave(), afterSave() 메소드 추가. (84575576)


## 0.0.5
2020년 02월 17일
- FieldWidget keyPostFix 인자 지원. (07d6db66)
- ArrayType: 'key' 옵션 추가. 이 옵션을 이용하면 입력하는 키의 종류를 제한할 수 있습니다. (76c5a7cc, b8f306c7)
- 기타 자잘한 수정 (cc489f15, 1cb243ee, b8f306c7)


## 0.0.4
2020년 02월 16일
- ScriptRegistrationInitiator 추가. (ba2fcd6c)
- MediaLibrarySelectorWidget, 새 파라미터 'library', 'params' 지원. (502f928d)
- AutoDiscoverClassFinder 의 contextRule 설정 삭제. (e2a1f928)
- 자잘한 수정 (9e3de97e, 051f6523) 


## 0.0.3
2020년 02월 14일
- JQuery UI 아이콘 이미지 추가 (ccc1d38b)


## 0.0.2
2020년 02월 13일
- DatePickerWidget 이 이제는 시간도 입력받을 수 있음. (bc9d3e85, d585b804, 6c100853)
- SelectWidget 은 augmentation 속성을 지원하지 않을 것임. (25d66ad9)
- '필수' 표시 수정. 이제 '\[필수\]'라고 붉은색 글씨로 필수 필드를 표시함. (26e00d69, ae734c2f)
- CustomPostAdminInitiator 기능 수정. (b025b053)
  - 커스텀 필드, 커스텀 정렬 필드 처리를 지원.
  - 메타 박스 저장 콜백 액션이 관리자 싱글 페이지에서만 동작하도록 수정.
- 자잘한 기능 개선 (ef82c0e4, 66d82a83, 617c6c28, )


## 0.0.1-alpha.3
2020년 2월 10일
- 스타터 할당 시점 조정 (fde983b5)
- 날짜가 올바르게 시간대에 맞춰 출력되지 않는 버그 수정 (1e37e727)
- 플러그인 활성/비활성화에 제대로 대응하지 않던 것 구현 (3e79b164)
- populateContext() 메소드 버그 수정 (608d3a4a)
- 툴팁 출력시 따옴표, 쌍따옴표를 이스케이프시 키지 않아 태그가 망가지던 문제 수정 (42929be2)
- 필드 키가 존재하지 않는 경우 리턴되는 false 값과 실제 설정된 false 값과의 모호성 제거 (572857db)


## 0.0.1-alpha.2
2020년 01월 31일

- BaseAspect 클래스 구현 (9a29453c)
- MetaFieldModel 에서 'objectSubType' 속성 생략시 'subType'을 참조하도록 수정 (b8b491b2)


## 0.0.1-alpha.1
2020년 01월 29일

- 버그 수정 (2b6d5b7a)


## 0.0.0
2020년 01년 23일

* development 브랜치에서 master 브랜치로 변경.
