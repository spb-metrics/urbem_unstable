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
    * Classe de mapeamento da tabela DIVIDA.MODALIDADE_PARCELA
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATModalidadeParcela.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.07
*/

/*
$Log$
Revision 1.4  2007/07/20 20:55:11  cercato
correcao para exclusao de modalidade.

Revision 1.3  2007/02/09 18:28:54  cercato
correcoes para divida.cobranca

Revision 1.2  2006/10/05 15:03:37  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/09/25 14:54:56  cercato
classes de mapeamento para funcionamento da modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATModalidadeParcela extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATModalidadeParcela()
    {
        parent::Persistente();
        $this->setTabela('divida.modalidade_parcela');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_modalidade');

        $this->AddCampo('cod_modalidade','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,true);
        $this->AddCampo('num_regra','integer',true,'',true,false);

        $this->AddCampo('vlr_limite_inicial','numeric',true,'14,2',false,false);
        $this->AddCampo('vlr_limite_final','numeric',true,'14,2',false,false);
        $this->AddCampo('qtd_parcela','integer',true,'',false,false);
        $this->AddCampo('vlr_minimo','numeric',true,'14,2',false,false);
    }

}// end of class

?>
