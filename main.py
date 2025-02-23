num1 = 0
num2 = 0
result= 0
op ="" 

while True:
    num1 = float( input ("Digite o primeiro número:"))
    op = input("Digite a operaçao matematica a ser feita:")
    num2 = float( input ("Digite o segundo número:"))

    if op =="=":
        result = num1 + num2
    
    elif op == "-":
        result = num1 - num2
    
    elif op == "*":
        result = num1 * num2
    else: 
        print("operaçao nao reconhecida!")
        print("{}{}{}={}".format(num1,op,num2, result)) 

