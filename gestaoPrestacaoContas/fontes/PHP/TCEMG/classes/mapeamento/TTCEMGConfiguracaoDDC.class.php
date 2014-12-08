<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento da tabela tcemg.configuracao_ddc
    * Data de Criação: 11/03/2014
    
    
    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Arthur Cruz
    
    * @package URBEM
    * @subpackage Mapeamento
*/

include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );

class TTCEMGConfiguracaoDDC extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGConfiguracaoDDC()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configuracao_ddc');
        
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,mes_referencia,cod_entidade,nro_contrato_divida');
        
        $this->AddCampo('exercicio'                          , 'varchar',  true,    '4',   true, false);
        $this->AddCampo('mes_referencia'                     , 'integer',  true,     '',   true, false);
        $this->AddCampo('cod_entidade'                       , 'integer',  true,     '',   true, false);
				$this->AddCampo('cod_orgao'                          , 'integer',  false,   '2',  false, false);
        $this->AddCampo('cod_norma'                	     		 , 'varchar',  true,    '6',  false, false);
        $this->AddCampo('nro_contrato_divida'                , 'varchar',  true,   '30',   true, false);
        $this->AddCampo('dt_assinatura'                      , 'date'   ,  true,     '',  false, false);
        $this->AddCampo('contrato_dec_lei'                   , 'integer',  false,    '',  false, false);
        $this->AddCampo('objeto_contrato_divida'             , 'text'   ,  true,     '',  false, false);
        $this->AddCampo('especificacao_contrato_divida'      , 'text'   ,  true,     '',  false, false);
        $this->AddCampo('tipo_lancamento'                    , 'varchar',  true,    '2',  false, false);
        $this->AddCampo('numcgm'                             , 'integer',  true,     '',  false, false);
        $this->AddCampo('justificativa_cancelamento'         , 'text'   , false,     '',  false, false);
        $this->AddCampo('valor_saldo_anterior'               , 'numeric',  true, '14,2',  false, false);
        $this->AddCampo('valor_contratacao'                  , 'numeric',  true, '14,2',  false, false);
        $this->AddCampo('valor_amortizacao'                  , 'numeric',  true, '14,2',  false, false);
        $this->AddCampo('valor_cancelamento'                 , 'numeric',  true, '14,2',  false, false);
        $this->AddCampo('valor_encampacao'                   , 'numeric',  true, '14,2',  false, false);
        $this->AddCampo('valor_atualizacao'                  , 'numeric',  true, '14,2',  false, false);
        $this->AddCampo('valor_saldo_atual'                  , 'numeric',  true, '14,2',  false, false);
        
    }
    
    function recuperaDadosDDC(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaDadosDDC().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaDadosDDC(){
	$stSql = " SELECT configuracao_ddc.exercicio
			 			 		  , mes_referencia
			 			 		  , cod_entidade
			 			 		  , cod_orgao
			 			 		  , configuracao_ddc.cod_norma AS nro_lei_autorizacao
			 			 		  , to_char(t_norma.dt_assinatura,'dd/mm/yyyy') AS dt_lei_autorizacao 
			 			 		  , to_char(t_norma.dt_publicacao,'dd/mm/yyyy') AS dt_publicacao_lei_Autorizacao 
			 			 		  , nro_contrato_divida
			 			 		  , to_char(configuracao_ddc.dt_assinatura,'dd/mm/yyyy') AS dt_assinatura
			 			 		  , contrato_dec_lei
			 			 		  , objeto_contrato_divida
			 			 		  , especificacao_contrato_divida
			 			 		  , tipo_lancamento
			 			 		  , configuracao_ddc.numcgm
			 			 		  , sw_cgm.nom_cgm
			 			 		  , justificativa_cancelamento
			 			 		  , REPLACE(valor_saldo_anterior::VARCHAR, '.',',') AS valor_saldo_anterior
			 			 		  , REPLACE(valor_contratacao::VARCHAR, '.',',') AS valor_contratacao
			 			 		  , REPLACE(valor_amortizacao::VARCHAR, '.',',') AS valor_amortizacao 
			 			 		  , REPLACE(valor_cancelamento::VARCHAR, '.',',') AS valor_cancelamento 
			 			 		  , REPLACE(valor_encampacao::VARCHAR, '.',',') AS valor_encampacao 
			 			 		  , REPLACE(valor_atualizacao::VARCHAR, '.',',') AS valor_atualizacao 
			 			 		  , REPLACE(valor_saldo_atual::VARCHAR, '.',',') AS valor_saldo_atual 
		     			 FROM tcemg.configuracao_ddc
	       INNER JOIN normas.norma AS t_norma
		       			 ON t_norma.cod_norma  = configuracao_ddc.cod_norma
		       		 JOIN sw_cgm
		       		   ON sw_cgm.numcgm = configuracao_ddc.numcgm
		    		  WHERE configuracao_ddc.exercicio    = '".$this->getDado('exercicio')."'
		      			AND mes_referencia     					  = ".$this->getDado('mes_referencia')."
		      		  AND configuracao_ddc.cod_entidade = ".$this->getDado('cod_entidade');
	return $stSql;
    }
    
    function recuperaLeisDDC(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaLeisDDC().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaLeisDDC(){
	$stSql = " SELECT t_ddc.exercicio
		         ,t_ddc.mes_referencia
		         ,t_ddc.cod_entidade
		         ,t_ddc.cod_norma AS nro_lei_autorizacao
		     FROM tcemg.configuracao_ddc AS t_ddc
		     JOIN normas.norma AS t_norma
		       ON t_norma.cod_norma = t_ddc.cod_norma
		    WHERE t_ddc.exercicio      = '".$this->getDado('exercicio')."' 
		      AND t_ddc.mes_referencia = ".$this->getDado('mes_referencia')." 
		      AND t_ddc.cod_entidade   = ".$this->getDado('cod_entidade')."
		 GROUP BY nro_lei_autorizacao
		         ,t_ddc.exercicio
			 ,t_ddc.mes_referencia
			 ,t_ddc.cod_entidade ";
	return $stSql;
    }
    
    function recuperaDadosMensalDDC10(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaDadosMensalDDC10().$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaDadosMensalDDC10(){
	$stSql  = "  SELECT '10' AS tipo_registro
			    ,LPAD(t_ddc.cod_orgao::varchar,2,'0') as cod_orgao
			    ,t_ddc.cod_norma AS nro_lei_autorizacao
			    ,to_char(t_norma.dt_assinatura,'ddmmyyyy') AS dt_lei_autorizacao 
			    ,to_char(t_norma.dt_publicacao,'ddmmyyyy') AS dt_publicacao_lei_Autorizacao
		       FROM tcemg.configuracao_ddc AS t_ddc
	         INNER JOIN normas.norma AS t_norma
			 ON t_norma.cod_norma    = t_ddc.cod_norma
		      WHERE t_ddc.exercicio      = '".$this->getDado('exercicio')."' 
		        AND t_ddc.mes_referencia = ".$this->getDado('mes_referencia')." 
		        AND t_ddc.cod_entidade   in (".$this->getDado('cod_entidade').")
		   GROUP BY tipo_registro
			    ,cod_orgao
			    ,nro_lei_autorizacao
			    ,dt_lei_autorizacao 
			    ,dt_publicacao_lei_Autorizacao";     
        return $stSql;
    }
    
    function recuperaDadosMensalDDC20(&$rsRecordSet, $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaDadosMensalDDC20().$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaDadosMensalDDC20(){
	$stSql  = "  SELECT '20' AS tipo_registro
			    ,LPAD(t_ddc.cod_orgao::varchar,2,'0') as cod_orgao
			    ,nro_contrato_divida
			    ,to_char(t_ddc.dt_assinatura,'ddmmyyyy') AS dt_assinatura
			    ,contrato_dec_lei
			    ,t_ddc.cod_norma AS nro_lei_autorizacao
			    ,to_char(t_norma.dt_assinatura,'ddmmyyyy') AS dt_lei_autorizacao
			    ,objeto_contrato_divida
			    ,especificacao_contrato_divida
		       FROM tcemg.configuracao_ddc AS t_ddc
		 INNER JOIN normas.norma AS t_norma
			 ON t_norma.cod_norma    = t_ddc.cod_norma
		      WHERE t_ddc.exercicio      = '".$this->getDado('exercicio')."' 
		        AND t_ddc.mes_referencia = ".$this->getDado('mes_referencia')." 
		        AND t_ddc.cod_entidade  in (".$this->getDado('cod_entidade').") ";     
        return $stSql;
    }
    
    function recuperaDadosMensalDDC30(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaDadosMensalDDC30().$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaDadosMensalDDC30(){
	$stSql  = "  SELECT '30' AS tipo_registro
			    					, LPAD(configuracao_ddc.cod_orgao::varchar,2,'0') as cod_orgao
			    					, nro_contrato_divida
			    					, to_char(configuracao_ddc.dt_assinatura,'ddmmyyyy') AS dt_assinatura
			    					, contrato_dec_lei
			    					, configuracao_ddc.cod_norma AS nro_lei_autorizacao
			    					, LPAD(tipo_lancamento::VARCHAR,2,'0') AS tipo_lancamento
			    					, CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
			    								CASE WHEN sw_cgm_pessoa_fisica.cod_nacionalidade = 1 THEN
			    										1
			    								ELSE
			    										3
			    								END
			    						ELSE
			    								2
			    						END AS tipo_documento_credor
			    					, CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
			    								sw_cgm_pessoa_fisica.cpf
			    						ELSE
			    								sw_cgm_pessoa_juridica.cnpj
			    						END AS nro_documento_credor
			    					, justificativa_cancelamento
			    					, REPLACE(valor_saldo_anterior::VARCHAR, '.',',') AS vl_saldo_anterior
			    					, REPLACE(valor_contratacao::VARCHAR, '.',',')    AS vl_contratacao
			    					, REPLACE(valor_amortizacao::VARCHAR, '.',',')    AS vl_amortizacao 
			    					, REPLACE(valor_cancelamento::VARCHAR, '.',',')   AS vl_cancelamento 
			    					, REPLACE(valor_encampacao::VARCHAR, '.',',')     AS vl_encampacao 
			    					, REPLACE(valor_atualizacao::VARCHAR, '.',',')    AS vl_atualizacao 
			    					, REPLACE(valor_saldo_atual::VARCHAR, '.',',')    AS vl_saldo_atual 
					       FROM tcemg.configuracao_ddc
					 INNER JOIN normas.norma AS t_norma
			 						 ON t_norma.cod_norma = configuracao_ddc.cod_norma
			 					 JOIN sw_cgm
			 					   ON sw_cgm.numcgm = configuracao_ddc.numcgm
			 			LEFT JOIN sw_cgm_pessoa_fisica
			 						 ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
			 			LEFT JOIN sw_cgm_pessoa_juridica
			 						 ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
		      			WHERE configuracao_ddc.exercicio      = '".$this->getDado('exercicio')."' 
		        			AND configuracao_ddc.mes_referencia = ".$this->getDado('mes_referencia')." 
		        			AND configuracao_ddc.cod_entidade  in (".$this->getDado('cod_entidade').") "; 
        return $stSql;
    }
	
	public function __destruct(){}

}
?>