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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Sena

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.1  2007/10/17 13:18:25  bruce
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOBalancoApcaaaa extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TTGOBalancoApcaaaa()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.balanco_apbaaaa_tipo_combustivel");

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio');

        $this->AddCampo( 'cod_plano' ,'integer' ,true, ''   ,true ,true  );
        $this->AddCampo( 'exercicio' ,'char' ,true, '4'   ,true ,true  );
        $this->AddCampo( 'tipo_lancamento' ,'integer' ,true, '' ,false ,false );
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT  balanco_apcaaaa.cod_plano
                 ,  tipo_lancamento
                 ,  cod_estrutural
                 ,  nom_conta
              FROM  tcmgo.balanco_apcaaaa
        INNER JOIN  contabilidade.plano_analitica
                ON  plano_analitica.cod_plano = balanco_apcaaaa.cod_plano
               AND  plano_analitica.exercicio = balanco_apcaaaa.exercicio
        INNER JOIN  contabilidade.plano_conta
                ON  plano_conta.cod_conta = plano_analitica.cod_conta
               AND  plano_conta.exercicio = plano_analitica.exercicio
             WHERE  balanco_apcaaaa.exercicio = '".$this->getDado('exercicio')."' ";
        if ( $this->getDado('tipo_lancamento') ) {
            $stSql.= " AND balanco_apcaaaa.tipo_lancamento = ".$this->getDado('tipo_lancamento')." ";
        }
        if ( $this->getDado('cod_plano') ) {
            $stSql.= " AND balanco_apcaaaa.cod_plano = ".$this->getDado('cod_plano')." ";
        }

        return $stSql;
    }

}
