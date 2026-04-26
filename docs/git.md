- Dùng **git checkout -b [tên nhánh]** để tạo nhánh mới khi làm một chức năng hãy chỉnh sửa nào đó.
- Quan trọng: Sau khi push vào nhánh (nhánh vừa tạo) -> lên github tạo pull request xong đợi kiểm duyệt rồi mới merge vào main nhằm tránh bị conflict.
###
**(Lưu ý: Phải hiểu gõ cách làm PR nếu không, khi merge code sẽ bị nhập lung tung lúc đó fix ỉa. Còn không, tốt nhất nhờ người giữ repo tạo giùm, còn lười thì push xong để đó, tự có người làm giùm)**
- Sau khi merge thành công dùng 2 câu lệnh (Lưu ý: Đã merge thành công mới xử dụng 2 lệnh này):
```bash
git branch -d feature/login        # Xóa local
git push origin --delete feature/login # Xóa remote
```
- Luôn ```git fetch``` để xem có gì thay đổi trên nhánh main không rồi ```git pull``` để lấy code mới nhất về, đặc biệt file web_shop_php.sql cần lưu ý nếu có thay đổi nên báo cho team.