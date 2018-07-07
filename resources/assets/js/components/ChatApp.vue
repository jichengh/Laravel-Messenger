<template>
    <div class="chat-app">
        <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
        <ContactsList :contacts="contacts" @selected="startConversationWith"/>

    </div>
</template>

<script>
    import Conversation from './Conversation';
    import ContactsList from './ContactsList';

    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                selectedContact: null,
                messages: [],
                contacts: []
            };
        },
        mounted() {
            Echo.private(`messages.${this.user.id}`)
                .listen('NewMessage', (event) => {
                    console.log(event.message);
                    this.handleIncoming(event.message);
                });

            axios.get('/contacts')
                .then((response) => {
                this.contacts = response.data;
            });
        },
        methods: {
            startConversationWith(contact) {
                this.updatedUnreadCount(contact, true);
                axios.get(`/conversation/${contact.id}`)
                    .then((response)=> {
                    this.messages = response.data;
                    this.selectedContact = contact;
                });
            },
            saveNewMessage(message) {
                // push new message to messages array
                this.messages.push(message);
            },
            handleIncoming(message) {
                if (this.selectedContact && message.from_user_id == this.selectedContact.id) {
                    this.saveNewMessage(message);
                    return;
                }

                this.updatedUnreadCount(contact.fromContact, false);
            },
            updatedUnreadCount(contact, reset) {
                this.contacts = this.contacts.map((single) => {
                    if (single.id != contact.id) {
                        return single;
                    }

                    if (reset) {
                        single.unread = 0;
                    } else {
                        single.unread += 1;
                    }

                    return single;
                });
            }
        },
        components: {Conversation, ContactsList}
    }
</script>

<style lang="scss" scoped>
    .chat-app{
        display: flex;
    }
</style>
