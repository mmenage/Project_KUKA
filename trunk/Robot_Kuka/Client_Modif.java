package application;
import java.io.*;
import java.net.*;
import java.util.ArrayList;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class Client_Modif {
	protected static final int PORT = 3351;
	private String serveur = "192.168.1.10";
	private BufferedReader in = null;
	private	BufferedReader console = null;
	private PrintStream out = null;
	private Socket s = null;
	


	public Client_Modif(String serveur) {
		this.serveur = serveur;
		try {
			this.s = new Socket (serveur, PORT);

			this.in = new BufferedReader(new InputStreamReader(this.s.getInputStream()));
			this.console = new BufferedReader(new InputStreamReader(System.in));
			this.out = new PrintStream(this.s.getOutputStream());
			System.out.println("Connexion �tablie avec " + this.s.getInetAddress() + " sur le port " + this.s.getPort());
		} catch (IOException e) {
			System.out.println("�chec de la connexion");
		}
	}

	public String lance() {
		String mess = null;
		try{
			while (true) {
//				mess = this.console.readLine();
//				this.out.println(mess);
				mess = this.in.readLine();
				if (mess == null) {
					System.out.println("Connexion termin�e");
					s.close();
					break;
				}
				System.out.println(mess);
				s.close();
				break;
			}
		}
		catch (IOException e) {
			System.err.println(e);
			System.out.println("Error");
		}
		
		return mess;
	} 
	//
	public static double ConvertionX(double valueX){
		double valueXConverted = 0.0;
		// pour une feuille de 25x18 (cm)
		// valueXConverted = valueX / 2.5;
		valueXConverted = valueX / 1.8;
		
		return valueXConverted;
	}
	public static double ConvertionY(double valueY){
		double valueYConverted = 0.0;
		// pour une feuille de 25x18 (cm)
		//valueYConverted = valueY / 1.8;
		valueYConverted = valueY / 2.5;
		
		return valueYConverted;
	}
	
	public static Vector2[] traitement(String chaine) {
		
		Vector2[] tableau;
		
		
		
		double x = 0.0;
		double y = 0.0;
		JSONParser p = new JSONParser();
		JSONObject o = null;
		int nbPoint = 0;
	    try {
	    	o = (JSONObject) p.parse(chaine);
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			System.out.println(e.getMessage());
			
		}
	    
	    ArrayList<JSONObject> liste = (ArrayList<JSONObject>) o.get("dessin");
	    
	    tableau = new Vector2[liste.size()];
	    
	   	for(JSONObject j: liste) {
	   		
	   		x = Double.parseDouble(j.get("x").toString());
	   		y = Double.parseDouble(j.get("y").toString());
	   		
	   		tableau[nbPoint] = new Vector2(ConvertionX(x), ConvertionY(y));
	   		
	   		System.out.println("x" + nbPoint + ":" + ConvertionX(x));
	   		System.out.println("y" + nbPoint + ":" + ConvertionY(y));
	   		nbPoint++;
	   	}
	   	
	   	return tableau;
	   	
	   	
	}
	
	public static void main(String [] args) {
		String message = null;
		String leServeur = "192.168.1.10";	
		if (args.length==1) {
			leServeur = args[0];
		}
		
		Client_Modif c = new Client_Modif(leServeur);
		
		message = c.lance();
		/*JSONObject p1 = new JSONObject();
		p1.put("x", 0);
		p1.put("y", 0);
		
		JSONObject p2 = new JSONObject();
		p2.put("x", 1);
		p2.put("y", 0);
		
		JSONObject p3 = new JSONObject();
		p3.put("x", 0);
		p3.put("y", 1);
		
		ArrayList<JSONObject> liste = new ArrayList<JSONObject>();
	    liste.add(p1);
	    liste.add(p2);
	    liste.add(p3);
	    
		JSONObject json = new JSONObject();
		json.put("dessin",liste);
		System.err.println(json.toJSONString());*/
		
		//traitement(json.toJSONString());
		traitement(message);
	}
}