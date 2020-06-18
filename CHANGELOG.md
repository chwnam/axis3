# CHANGELOG
## 0.1.17
2020년 06월 18일
-  CheckboxWidget 'extraDesc' 속성 추가 (26ad1f1e)


## 0.1.16
2020년 06월 05일 
- formatAttr() 함수 중 'value' 속성 수정 (f79b4947)


## 0.1.15
2020년 05월 26일
- 포스트 캐시 삭제된 다음의 대응 추가. (a9e23229) 
- MetaFieldHolderModel::claimMetaFieldModel() 인터페이스의 리턴 타입 삭제 (8c447149)


## 0.1.14
2020년 05월 15일
- BaseView::enqueueEjs() 메소드에서 EJS 파일이 출력되는 시점 조정 (b1147882)


## 0.1.13
2020년 05월 8일
- ScriptPropFilter 관련 인터페이스 수정 및 사용 편의성 확대. (3c0610ef, 766f707b, 2a26a1d7, b76e725f)
- 자잘한 수정. (a4a014d7, 0658d5cb)


## 0.1.12
2020년 04월 24일
- BaseView::enqueueEjs() 의 메소드 체인 구현 (bde09013)


## 0.1.11
2020년 04월 16일
- keyPostfix 지원 확대 (1a0103ba)
- select 태그 출력이 multiple 속성을 지원하도록 수정 (c13670ad)
- BaseView::getAssetUrl() 메소드 버그 수정 (0cb85d3e)
- listTag() 함수 추가. (089866df)


## 0.1.10
2020년 04월 07일
- mb_ord() 대신 uniOrd() 호환 함수 제작 (847bf027)


## 0.1.9
2020년 04월 07일
- 숨겨진 필드에 대한 옵션 추가 (0e0a9c79)
- createFromImmutable() 함수 추가. (93bc11c2)
- 자잘한 수정 (7a6a3592, acac6b21, 2a2996f4)


## 0.1.8
2020년 03월 27일
- autoload 'no' 옵션 정정 기능 추가. (09e87734)

  플러그인 활성화시 autoload='no'로 설정된 옵션 필드들 중 실제로 'yes'로 기록되어 있는 레코드를 전부
  'no'로 교정하는 기능을 추가. 

- 'accept' 속성 내 값의 중복 제거 루틴 추가. (ddff783d)

  accept 속성으로 쓰려고 주어진 배열에서 중복이 발생한 경우, 중복을 제거하고 출력하도록 수정. 

- filterStringList() 함수 추가. (b1ee2bd5) 
  
  문자열 목록을 받아 문자열 전후 공백이나 빈 항목 제거, 중복 제거 처리를 하는 함수.
  textarea 같은 한 줄에 하나씩 어떤 아이템을 입력받을 때 편리하게 처리되도록 의도함.  

- enqueueEjs() 메소드 기능 수정.

  enqueueEjs() 메소드에서 템플릿 아이디 입력시 'tmpl-'로 시작하지 않으면 접두 'tmpl-'을 붙여주도록 수정 (0e0499e8, de456f59)


## 0.1.7
2020년 03월 20일
- ScriptPropFilter 기능 추가. (bce4a640)
- HTML accept 속성에 대한 처리 추가. (51312931)
- 'splitHangul'에서  'decomposeHangul'로 함수 이름 변경. (3dc1e733)


## 0.1.6
2020년 03월 19일
- 함수 strSplit(), splitHangul(), josa(), 그리고 테스트 코드 추가 (a1c287aa)


## 0.1.5
2020년 03월 15일
0.1.4에 대해 코드 버그 수정이 이뤄지지는 않았습니다.
- ShortcodeInspector 구현 (f505c42a)
- 코드 업데이트 (2adff338, 9917d981)
 

## 0.1.4
2020년 03월 11일
- prism dependency 추가 (fe107692)
- PHP 7.0 에서 동작하지 않는 코드 수정 (561e7551)
- 옵션 그룹을 지원하도록 수정 (7703fff8)
- addSettingsSections(), addSettingsFields() 점근 제한 완화 (3b4602d4)


## 0.1.3
2020년 03월 10일
- 옵션 저장 전후로 콜백이 호출될 수 있도록 파라미터 추가 (4501f065)


## 0.1.2
2020년 03월 06일
- 버튼에도 disabled 속성 고려도록 수정 (a2de86d8)
- getDatetime() 함수에서 입력이 DateTimeImmutable 타입인 경우 처리되도록 수정 (6d77c9dc)


## 0.1.1
2020년 03월 01일
- splitArray() 버그 수정 (64a429cf)
- MetaFieldModel, OptionFieldModel 의 캐시 업데이트 기능 개선 (17d83cf5, 04e6f821) 
- SelectWidget, InputWidget 에 disabled 속성 추가 (39ff4185)
- enqueueEjs() 메소드 추가 (beb3b630)
- ArrayType 에서 import, export 시 ValueObject 와 제대로 연동되지 않던 문제 수정 (c259dce9, 04e6f821)
- Dynamic Return Type 플러그인을 위한 메타 파일 작성 (09d82f30)
- SettingsModelInterface 인터페이스 변경 (f1e118e4)
- MenuPageView setup() 옵션 'fixAdminHook' 추가 (9a2c23dc)


## 0.1.0
2020년 02월 27일
- 코드 안정성 향상 (96a84176, 81884ad1, 04a20640)
- property-meta-box.php 템플릿 수정 (b607256b)
- AutoDiscoverClassFinder API 변경 (93321dde)


## 0.0.9
2020년 02월 25일
- 함수 arrayKeyFirst(), arrayKeyLast() 추가 (2d9525a8)
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
