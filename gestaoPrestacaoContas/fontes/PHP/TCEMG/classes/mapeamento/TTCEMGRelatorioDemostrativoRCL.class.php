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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/08/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGRelatorioDemostrativoRCL.class.php 59719 2014-09-08 15:00:53Z franver $
  $Date: 2014-09-08 12:00:53 -0300 (Seg, 08 Set 2014) $
  $Author: franver $
  $Rev: 59719 $
*/

class TTCEMGRelatorioDemostrativoRCL extends Persistente
{
    
    public function TTCEMGRelatorioDemostrativoRCL()
    {
        parent::Persistente();
    }
    
    public function recuperaReceitasDemonstrativoRCL(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
	    $obConexao   = new Conexao;
	    $rsRecordSet = new RecordSet;
	    $stSql = $this->montaRecuperaReceitasDemonstrativoRCL().$stFiltro.$stOrdem;
	    $this->stDebug = $stSql;
	    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaReceitasDemonstrativoRCL()
    {
        $stSql = "
         SELECT cod_conta     
              , SUBSTR(nom_conta, 0,56) AS nom_conta
              , cod_estrutural
              , mes_1         
              , mes_2         
              , mes_3         
              , mes_4         
              , mes_5         
              , mes_6         
              , mes_7         
              , mes_8         
              , mes_9         
              , mes_10        
              , mes_11        
              , mes_12
              , cast((mes_1+mes_2+mes_3+mes_4+mes_5+mes_6+mes_7+mes_8+mes_9+mes_10+mes_11+mes_12) as numeric(14,2)) AS total
           FROM tcemg.fn_relatorio_demostrativo_rcl('".$this->getDado('exercicio')."','".$this->getDado('cod_entidades')."','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."',".$this->getDado('tipo_despesa').")
             AS retorno ( cod_conta      varchar
                        , nom_conta      varchar
                        , cod_estrutural varchar
                        , mes_1          numeric(14,2)
                        , mes_2          numeric(14,2)
                        , mes_3          numeric(14,2)
                        , mes_4          numeric(14,2)
                        , mes_5          numeric(14,2)
                        , mes_6          numeric(14,2)
                        , mes_7          numeric(14,2)
                        , mes_8          numeric(14,2)
                        , mes_9          numeric(14,2)
                        , mes_10         numeric(14,2)
                        , mes_11         numeric(14,2)
                        , mes_12         numeric(14,2)
                        );
        ";
        
        return $stSql;
    }
    
    public function recuperaDespesasDemonstrativoRCL(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
	    $obConexao   = new Conexao;
	    $rsRecordSet = new RecordSet;
	    $stSql = $this->montaRecuperaDespesasDemonstrativoRCL().$stFiltro.$stOrdem;
	    $this->stDebug = $stSql;
	    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaDespesasDemonstrativoRCL()
    {
        $stSql ="
            SELECT cod_conta     
                 , nom_conta     
                 , cod_estrutural
                 , mes_1         
                 , mes_2         
                 , mes_3         
                 , mes_4         
                 , mes_5         
                 , mes_6         
                 , mes_7         
                 , mes_8         
                 , mes_9         
                 , mes_10        
                 , mes_11        
                 , mes_12
                 , cast((mes_1+mes_2+mes_3+mes_4+mes_5+mes_6+mes_7+mes_8+mes_9+mes_10+mes_11+mes_12) as numeric(14,2)) AS total
              FROM tcemg.fn_relatorio_demostrativo_rcl_despesa('".$this->getDado('exercicio')."','".$this->getDado('cod_entidades')."','".$this->getDado('dt_incial')."','".$this->getDado('dt_final')."',".$this->getDado('tipo_despesa').",'".$this->getDado('tipo_situacao')."')
                AS retorno ( cod_conta      varchar
                           , nom_conta      varchar
                           , cod_estrutural varchar
                           , mes_1          numeric(14,2)
                           , mes_2          numeric(14,2)
                           , mes_3          numeric(14,2)
                           , mes_4          numeric(14,2)
                           , mes_5          numeric(14,2)
                           , mes_6          numeric(14,2)
                           , mes_7          numeric(14,2)
                           , mes_8          numeric(14,2)
                           , mes_9          numeric(14,2)
                           , mes_10         numeric(14,2)
                           , mes_11         numeric(14,2)
                           , mes_12         numeric(14,2)
                           );        
        ";
        return $stSql;
    }
	
	public function __destruct(){}

}

?>