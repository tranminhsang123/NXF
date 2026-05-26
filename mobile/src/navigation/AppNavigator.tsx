import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { useAuth } from '../context/AuthContext';
import { AuthScreen } from '../screens/AuthScreen';
import { AdminTabScreen } from '../screens/tabs/AdminTabScreen';
import { CourseDetailScreen } from '../screens/tabs/CourseDetailScreen';
import { CourseSectionListScreen } from '../screens/tabs/CourseSectionListScreen';
import { CourseSectionDetailScreen } from '../screens/tabs/CourseSectionDetailScreen';
import { CoursesTabScreen } from '../screens/tabs/CoursesTabScreen';
import { FlashcardsTabScreen } from '../screens/tabs/FlashcardsTabScreen';
import { HomeTabScreen } from '../screens/tabs/HomeTabScreen';
import { KanjiTabScreen } from '../screens/tabs/KanjiTabScreen';
import { LearnHubScreen } from '../screens/tabs/LearnHubScreen';
import { LessonDetailScreen } from '../screens/tabs/LessonDetailScreen';
import { LessonsTabScreen } from '../screens/tabs/LessonsTabScreen';
import { ProfileTabScreen } from '../screens/tabs/ProfileTabScreen';
import { ProgressTabScreen } from '../screens/tabs/ProgressTabScreen';
import { SearchTabScreen } from '../screens/tabs/SearchTabScreen';
import { SocialTabScreen } from '../screens/tabs/SocialTabScreen';
import { VocabularyTabScreen } from '../screens/tabs/VocabularyTabScreen';
import { playfulColors } from '../theme/duolingo';
import { ui } from '../theme/ui';

const Stack = createNativeStackNavigator();
const Tabs = createBottomTabNavigator();
const LearnStack = createNativeStackNavigator<LearnStackParamList>();

const TAB_META: Record<string, { label: string; icon: string }> = {
  Home: { label: 'Trang chủ', icon: '🏠' },
  Learn: { label: 'Học tập', icon: '📚' },
  Social: { label: 'Cộng đồng', icon: '💬' },
  Profile: { label: 'Tài khoản', icon: '👤' },
  Admin: { label: 'Quản trị', icon: '🛡️' },
};

export type LessonsStackParamList = Pick<LearnStackParamList, 'LessonList' | 'LessonDetail'>;

export type LearnStackParamList = {
  LearnHub: undefined;
  LessonList: undefined;
  LessonDetail: { lessonNumber: number };
  Kanji: undefined;
  Vocabulary: undefined;
  Courses: undefined;
  CourseDetail: { level: string; title: string };
  CourseSectionList: { level: string; sectionType: string; sectionTitle: string };
  CourseSectionDetail: { level: string; sectionType: string; itemKey: string; itemTitle: string; sectionTitle: string };
  Progress: undefined;
  Search: undefined;
  Flashcards: undefined;
};

function LearnNavigator() {
  return (
    <LearnStack.Navigator
      screenOptions={{
        headerStyle: { backgroundColor: playfulColors.softBlue },
        headerTintColor: playfulColors.textPrimary,
        headerTitleStyle: { ...ui.text.bodyStrong, fontSize: 16, fontWeight: '800' },
      }}
    >
      <LearnStack.Screen name="LearnHub" component={LearnHubScreen} options={{ title: 'Trung tâm học tập' }} />
      <LearnStack.Screen name="LessonList" component={LessonsTabScreen} options={{ title: 'Bài học Minna' }} />
      <LearnStack.Screen name="LessonDetail" component={LessonDetailScreen} options={{ title: 'Chi tiết bài học' }} />
      <LearnStack.Screen name="Kanji" component={KanjiTabScreen} options={{ title: 'Hán tự' }} />
      <LearnStack.Screen name="Vocabulary" component={VocabularyTabScreen} options={{ title: 'Từ vựng' }} />
      <LearnStack.Screen name="Courses" component={CoursesTabScreen} options={{ title: 'Lộ trình học' }} />
      <LearnStack.Screen name="CourseDetail" component={CourseDetailScreen} options={{ title: 'Chi tiết lộ trình' }} />
      <LearnStack.Screen name="CourseSectionList" component={CourseSectionListScreen} options={{ title: 'Danh sách mục học' }} />
      <LearnStack.Screen name="CourseSectionDetail" component={CourseSectionDetailScreen} options={{ title: 'Nội dung mục học' }} />
      <LearnStack.Screen name="Progress" component={ProgressTabScreen} options={{ title: 'Tiến độ học tập' }} />
      <LearnStack.Screen name="Search" component={SearchTabScreen} options={{ title: 'Tìm kiếm' }} />
      <LearnStack.Screen name="Flashcards" component={FlashcardsTabScreen} options={{ title: 'Thẻ ghi nhớ' }} />
    </LearnStack.Navigator>
  );
}

function SplashScreen() {
  return (
    <View style={styles.splash}>
      <ActivityIndicator size="large" color="#2563eb" />
    </View>
  );
}

export function AppNavigator() {
  const { user, isBootstrapping } = useAuth();
  const insets = useSafeAreaInsets();

  if (isBootstrapping) {
    return <SplashScreen />;
  }

  return (
    <NavigationContainer>
      {user ? (
        <Tabs.Navigator
          screenOptions={({ route }) => ({
            headerShown: false,
            tabBarActiveTintColor: playfulColors.brandDark,
            tabBarInactiveTintColor: playfulColors.textSecondary,
            tabBarHideOnKeyboard: true,
            tabBarIcon: ({ color, focused }) => (
              <Text style={[styles.tabIcon, { color }, focused && styles.tabIconActive]}>
                {TAB_META[route.name]?.icon ?? '•'}
              </Text>
            ),
            tabBarStyle: {
              position: 'absolute',
              left: ui.spacing.md,
              right: ui.spacing.md,
              // Chỉ hạ nhẹ khỏi mép màn hình; vùng gesture/home indicator xử lý bằng paddingBottom (tránh cộng inset 2 lần làm thanh “bay” lên cao).
              bottom: 8,
              borderRadius: ui.radius.lg,
              height: 56 + insets.bottom,
              paddingBottom: Math.max(10, insets.bottom),
              paddingTop: 6,
              backgroundColor: '#ffffff',
              borderTopWidth: 0,
              shadowColor: '#1f2a44',
              shadowOffset: { width: 0, height: 8 },
              shadowOpacity: 0.1,
              shadowRadius: 16,
              elevation: 8,
            },
            tabBarItemStyle: {
              justifyContent: 'center',
              alignItems: 'center',
            },
            tabBarLabel: ({ color, focused }) => (
              <Text style={[styles.tabLabel, { color }, focused && styles.tabLabelActive]}>{TAB_META[route.name]?.label ?? route.name}</Text>
            ),
            tabBarLabelStyle: {
              ...ui.text.caption,
              fontWeight: '800',
              fontSize: 11,
              marginTop: -2,
            },
            sceneStyle: {
              paddingTop: Math.max(insets.top, ui.spacing.xs),
            },
          })}
        >
          <Tabs.Screen name="Home" component={HomeTabScreen} options={{ title: TAB_META.Home.label }} />
          <Tabs.Screen name="Learn" component={LearnNavigator} options={{ title: TAB_META.Learn.label }} />
          <Tabs.Screen name="Social" component={SocialTabScreen} options={{ title: TAB_META.Social.label }} />
          <Tabs.Screen name="Profile" component={ProfileTabScreen} options={{ title: TAB_META.Profile.label }} />
          {user.role === 'admin' ? <Tabs.Screen name="Admin" component={AdminTabScreen} options={{ title: TAB_META.Admin.label }} /> : null}
        </Tabs.Navigator>
      ) : (
        <Stack.Navigator>
          <Stack.Screen name="Auth" component={AuthScreen} options={{ headerShown: false }} />
        </Stack.Navigator>
      )}
    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  splash: {
    flex: 1,
    backgroundColor: playfulColors.page,
    alignItems: 'center',
    justifyContent: 'center',
  },
  tabIcon: {
    fontSize: 16,
    marginBottom: 2,
  },
  tabIconActive: {
    transform: [{ scale: 1.05 }],
  },
  tabLabel: {
    fontSize: 11,
    fontWeight: '700',
  },
  tabLabelActive: {
    fontWeight: '800',
  },
});
